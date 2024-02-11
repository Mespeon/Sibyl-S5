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
use App\Models\UserStudentProfiles;
use App\Models\UserRoles;
use App\Models\UserDepartments;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\ServerErrorException;

class AuthorizationService {
    protected $users, $userProfiles, $userStudentProfiles, $userRoles, $userDepartments;

    public function __construct(
        User $users,
        UserProfiles $userProfiles,
        UserStudentProfiles $userStudentProfiles,
        UserRoles $userRoles,
        UserDepartments $userDepartments
    ) {
        $this->users = $users;
        $this->userProfiles = $userProfiles;
        $this->userStudentProfiles = $userStudentProfiles;
        $this->userRoles = $userRoles;
        $this->userDepartments = $userDepartments;
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
        return ['payload' => $payload, 'token' => $tokenResponse->original['token']];
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
        $department = $data->only(['department']);

        // If the registering user uses a Student role, then create params for student profile.
        $studentProfile = ($data->role == 3) ? $data->only(['course', 'year', 'section']) : null;

        try {
            // Create user account.
            $createUserAccountTransaction = DB::transaction(function () use ($account, $profile, $role, $department, $studentProfile) {
                $user = $this->users->setNewUser($account);

                // Assign a user ID to profile, role, and department.
                $profile['user_id'] = $role['user_id'] = $department['user_id'] = $user->id;

                // Unset and re-set role ID in department and role.
                $role['role_id'] = (int) $role['role'];
                unset($role['role']);

                $department['department_id'] = (int) $department['department'];
                unset($department['department']);

                $this->userProfiles->setNewUserProfile($profile);
                $this->userRoles->setNewUserRole($role);
                $this->userDepartments->setNewUserDepartment($department);

                // If a student profile was set, attempt to create it as well.
                if ($studentProfile) {
                    $studentProfile['user_id'] = $user->id;
                    $studentProfile['course_id'] = $studentProfile['course'];
                    $studentProfile['year_level'] = $studentProfile['year'];
                    unset($studentProfile['course'], $studentProfile['year']);

                    $this->userStudentProfiles->setNewUserStudentProfile($studentProfile);
                }

                return ['account' => $user];
            }, 1);

            return $createUserAccountTransaction;
        }
        catch (\Exception $e) {
            throw new ServerErrorException("An error has occurred while attempting to create account: {$e->getMessage()}", '', '');
        }
    }
}