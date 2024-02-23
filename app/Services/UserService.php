<?php
namespace App\Services;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\UserProfiles;
use App\Models\UserStudentProfiles;
use App\Models\UserFacultyProfiles;
use App\Models\UserRoles;
use App\Models\UserDepartments;
use App\Models\UserSchools;

use App\Exceptions\ServerErrorException;

class UserService {

    public function __construct(
        protected User $users,
        protected UserProfiles $userProfiles,
        protected UserStudentProfiles $userStudentProfiles,
        protected UserFacultyProfiles $userFacultyProfiles,
        protected UserRoles $userRoles,
        protected UserDepartments $userDepartments,
        protected UserSchools $userSchools
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
        ->when(in_array(3, $data['roles']), function ($query) {
            $query->with(['student_profile' => function ($subquery) {
                $select = [
                    'user_student_profiles.user_id',
                    'user_student_profiles.department_id',
                    'departments.code AS department_code',
                    'departments.name AS department_name',
                    'user_student_profiles.course_id',
                    'courses.code AS course_code',
                    'courses.name AS course_name',
                    'user_student_profiles.year_level',
                    'user_student_profiles.section'
                ];

                $subquery->select($select)
                ->join('departments', 'departments.id', 'user_student_profiles.department_id')
                ->join('courses', 'courses.id', 'user_student_profiles.course_id');
            }]);
        })
        ->when(in_array(4, $data['roles']), function ($query) {
            $query->with(['faculty_profile' => function ($subquery) {
                $select = [
                    'user_faculty_profiles.user_id',
                    'user_faculty_profiles.department_id',
                    'departments.code AS department_code',
                    'departments.name AS department_name',
                    'user_faculty_profiles.employee_number',
                    'user_faculty_profiles.position_name'
                ];

                $subquery->select($select)
                ->join('departments', 'departments.id', 'user_student_profiles.department_id')
                ->join('courses', 'courses.id', 'user_student_profiles.course_id');
            }]);
        })
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
}