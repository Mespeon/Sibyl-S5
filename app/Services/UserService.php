<?php
namespace App\Services;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\UserProfiles;
use App\Models\UserRoles;

use App\Traits\CustomPagination;

use App\Exceptions\ServerErrorException;

class UserService {
    use CustomPagination;

    public function __construct(
        protected User $users,
        protected UserProfiles $userProfiles,
        protected UserRoles $userRoles
    ) {}

    /**
     * Returns the profile for a requested user.
     * 
     * @param array $data - data containing the filter values.
     * @param string $data.user_id - ID of user to retrieve the profile for.
     * @param array $data.roles - Array of role IDs specific to the user being retrieved.
     */
    public function profile(array $data) {
        return $this->userProfiles
        ->with(['user_account' => function ($subquery) {
            $select = [
                'users.id', 
                'username', 
                'status_id',
                'account_statuses.key AS status_key',
                'account_statuses.name AS status_name',
                'account_verified_at', 
                'last_login', 
                'last_password_change'
            ];
            $subquery->select($select)
            ->join('account_statuses', 'account_statuses.id', 'status_id');
        }])
        ->where('user_id', $data['user_id'])
        ->first();
    }

    /**
     * Update Profile
     * 
     * @param int userId - ID of user to be updated.
     * @param array data - array containing the update values.
     */
    public function updateProfile(int $userId, array $data) {
        try {
            $updateUserProfile = DB::transaction(function () use ($userId, $data) {
                $tapUpdateProfile = tap($this->userProfiles->where('user_id', $userId))->update($data);
                return $tapUpdateProfile->first();
            }, 1);

            return $updateUserProfile;
        }
        catch (\Exception $e) {
            throw new ServerErrorException("An error has occurred while attempting to update profile: {$e->getMessage()}", '', '');
        }
    }

    /**
     * Retrieve Users List
     * 
     * @param array data - array containing filters, if any.
     * @param Carbon query - a Carbon object containing the query parameters for the request.
     */
    public function retrieveUsersList(array $data, array $query) {
        $where = [];
        $orWhere = [];
        $order = ['users.created_at', 'DESC'];
        $daterange = null;

        if (isset($data['search']) && $data['search']) {
            array_push($where, ['users.username', 'LIKE', '%'.$data['search'].'%']);
        }

        if (isset($data['order_by']) && $data['order_by']) {
            $order = explode(',', $data['order_by']);
        }

        if (isset($data['daterange']) && $data['daterange']) {
            $requestedDaterange = explode(',', $data['daterange']);
            $daterange = [$requestedDaterange[0]." 00:00:00", $requestedDaterange[1]." 23:59:59"];
        }

        $collection = $this->users
        ->with(['role' => function ($query) {
            $query->addSelect([
                'roles.key AS role_key',
                'roles.name AS role_display_name'
            ])
            ->leftJoin('roles', 'roles.id', 'user_roles.role_id');
        }])
        ->with(['profile', 'account_status'])
        ->where($where)
        ->when($daterange, function ($query) use ($daterange) {
            $query->whereBetween('users.created_at', [$daterange[0], $daterange[1]]);
        })
        ->orderBy($order[0], $order[1])
        ->paginate($data['list_size'] ?? env('DEFAULT_LIST_SIZE'), ['*'], 'page')
        ->appends($query);

        return $this->customPagination($collection);
    }
}