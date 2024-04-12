<?php
namespace App\Services;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Claims\Issuer;
use Tymon\JWTAuth\Claims\IssuedAt;
use Tymon\JWTAuth\Claims\Expiration;

use App\Models\User;
use App\Models\UserProfiles;
use App\Models\UserRoles;
use App\Models\PasswordResetTokens;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\ServerErrorException;

class AuthorizationService {
    protected $users, $userProfiles, $userRoles, $passwordResetTokens;

    public function __construct(
        User $users,
        UserProfiles $userProfiles,
        UserRoles $userRoles,
        PasswordResetTokens $passwordResetTokens
    ) {
        $this->users = $users;
        $this->userProfiles = $userProfiles;
        $this->userRoles = $userRoles;
        $this->passwordResetTokens = $passwordResetTokens;
    }

    /**
     * Generate Guest Access Token Claims
     * 
     * Generates custom claims for a guest user.
	 * These claims are intended to be used only guest accounts and should not be bound to regular accounts.
     */
    public function generateGuestAccessTokenClaims($data) {
        $iss = new Issuer('Sibyl-System');
        $sub = $data['subject'];
        $aud = 'guest';
        $iat = new IssuedAt(Date::now());
        $exp = new Expiration(Date::now()->addHours($data['validity']));

        $guestAccessClaims = [
            'iat' => $iat,
            'exp' => $exp,
            'aud' => $aud,
            'iss' => $iss,
            'sub' => $sub
        ];

        return $guestAccessClaims;
    }

    public function generatePasswordResetAccessToken($data) {
        $iss = new Issuer('Sibyl-System');
        $sub = $data['subject'];
        $aud = 'user';
        $iat = new IssuedAt(Date::now());
        $exp = new Expiration(Date::now()->addMinutes($data['validity']));

        $passwordResetAccessClaims = [
            'iad' => $iat,
            'exp' => $exp,
            'aud' => $aud,
            'iss' => $iss,
            'sub' => $sub
        ];

        return $passwordResetAccessClaims;
    }

    /**
     * Construct Access Token
     * 
     * Constructs a token using custom claims.
     */
    public function constructAccessToken($claims) {
        $makeClaim = JWTFactory::customClaims($claims);
        $payload = JWTFactory::make($makeClaim);
        $token = JWTAuth::encode($payload);
        $tokenResponse = $this->respondWithToken($token->get());
        return ['payload' => $payload, 'token' => $tokenResponse->original['access_token']];
    }

    /**
	 * Returns a custom token response.
	 * @return Response
	 */
	public function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    /**
     * Create user account along with relevant entries.
     */
    public function createUserAccount($data) {
        // Split the data into its respective parts.
        $account = $data->only(['username', 'password']);
        $profile = $data->only(['first_name', 'middle_name', 'last_name', 'email_address', 'contact_number']);
        $role = $data->only(['role']);

        try {
            // Create user account.
            $createUserAccountTransaction = DB::transaction(function () use ($account, $profile, $role) {
                $user = $this->users->setNewUser($account);

                // Assign a user ID to profile, role, and department.
                $profile['user_id'] = $role['user_id'] = $department['user_id'] = $user->id;

                // Unset and re-set role ID in department and role.
                $role['role_id'] = (int) $role['role'];
                unset($role['role']);

                $this->userProfiles->setNewUserProfile($profile);
                $this->userRoles->setNewUserRole($role);

                return ['account' => $user];
            }, 1);

            return $createUserAccountTransaction;
        }
        catch (\Exception $e) {
            throw new ServerErrorException("An error has occurred while attempting to create account: {$e->getMessage()}", '', '');
        }
    }

    /**
     * Finds a user by username to determine existence.
     */
    public function findUser($username) {
        return $this->users->where('username', $username)->exists();
    }

    /**
     * Finds a user by email address to determine existence.
     */
    public function findUserByEmail($email) {
        return $this->userProfiles->where('email_address', $email)->exists();
    }

    /**
     * Retrieves a user password hash by user ID.
     */
    public function retrieveUserPassword(int $userId) {
        return $this->users->find($userId)->makeVisible('password');
    }

    /**
     * Retrieve user data by email.
     */
    public function retrieveUserByEmail($email) {
        return $this->userProfiles->where('email_address', $email)->get();
    }

    /**
     * Retrieves all user's roles.
     */
    public function retrieveUserRoles($data) {
        return $this->userRoles
        ->where('user_id', $data['user_id'])
        ->with('role')
        ->get();
    }

    /**
     * Finds a user role by role ID to determine existence.
     */
    public function findUserRole($data) {
        $where = [
            ['user_id', '=', $data['user_id']],
            ['role_id', '=', $data['role_id']]
        ];
        return $this->userRoles->where($where)->exists();
    }

    /**
     * Records the token issued when resetting passwords.
     */
    public function setNewPasswordResetToken($data) {
        foreach ($data as $key => $value) {
            $this->passwordResetTokens->$key = $value;
        }
        $this->passwordResetTokens->save();
        return $this->passwordResetTokens;
    }

    /**
     * Updates a user's password.
     * 
     * @param int data.user_id
     * @param string data.password
     */
    public function updateUserPassword(array $data) {
        $data['password'] = Hash::make($data['password']);
        return tap($this->users->where('id', $data['user_id'])->update([
            'password' => $data['password']
        ]));
    }

    /**
     * Verifies if the provided password matches with the old one.
     * 
     * @param int $data.user_id - ID of user changing their passwords.
     * @param string $data.current_password - Current password as sent from user input.
     */
    public function verifyPassword(array $data) {
        $userPassword = $this->retrieveUserPassword($data['user_id']);
        return password_verify($data['current_password'], $userPassword->password);
    }
}