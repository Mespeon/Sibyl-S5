<?php
namespace App\Services;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
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
use App\Models\UserDepartments;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\ServerErrorException;

class AuthorizationService {
    protected $users, $userProfiles, $userRoles, $userDepartments;

    public function __construct(
        User $users,
        UserProfiles $userProfiles,
        UserRoles $userRoles,
        UserDepartments $userDepartments
    ) {
        $this->users = $users;
        $this->userProfiles = $userProfiles;
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
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }

    public function createUserAccount($data) {
        // Split the data into its respective parts.
        $account = $data->only(['username', 'password']);
        $profile = $data->only(['first_name', 'middle_name', 'last_name', 'email_address', 'contact_number']);
        $role = $data->only(['role']);
        $department = $data->only(['department']);

        try {
            // Create user account.
            $account['status_id'] = 1;
            $userAccount = $this->users->setNewUser($account);

            // Create user profile.
            $profile['user_id'] = $userAccount->id;
            $userProfile = $this->userProfiles->setNewUserProfile($profile);
        }
        catch (\Exception $e) {
            throw new ServerErrorException("An error has occurred while attempting to create account: {$e->getMessage()}", '', '');
        }
    }
}