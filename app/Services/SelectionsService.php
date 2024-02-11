<?php
namespace App\Services;

use App\Models\AccountStatuses;
use App\Models\Courses;
use App\Models\Departments;
use App\Models\Roles;
use App\Models\Refcountries;
use App\Models\Refregions;
use App\Models\Refprovinces;
use App\Models\Refcitymuns;
use App\Models\Refbarangays;
use App\Models\Schools;

class SelectionsService {
    protected $accountStatuses, $courses, $departments, $roles,
    $countries, $regions, $provinces, $citymunicipalities, $barangays, $schools;

    public function __construct(
        AccountStatuses $accountStatuses,
        Courses $courses,
        Departments $departments,
        Roles $roles,
        Refcountries $countries,
        Refregions $regions,
        Refprovinces $provinces,
        Refcitymuns $citymunicipalities,
        Refbarangays $barangays,
        Schools $schools
    ) {
        $this->accountStatuses = $accountStatuses;
        $this->courses = $courses;
        $this->departments = $departments;
        $this->roles = $roles;
        $this->countries = $countries;
        $this->regions = $regions;
        $this->provinces = $provinces;
        $this->citymunicipalities = $citymunicipalities;
        $this->barangays = $barangays;
        $this->schools = $schools;
    }

    public function getRoles($data) {
        return $this->roles->get();
    }

    public function getAccountStatuses($data) {
        return $this->accountStatuses->get();
    }

    public function getCourses($data) {
        return $this->courses->get();
    }

    public function getDepartments($data) {
        return $this->departments->get();
    }

    public function getSchools($data) {
        return $this->schools->get();
    }

    public function getCountries($data) {
        return $this->countries->where('country_code', 'PH')->get();
    }

    public function getRegions($data) {
        return $this->regions->where('country_id', $data['country_id'])->get();
    }

    public function getProvinces($data) {
        return $this->provinces->where('regCode', $data['region_code'])->get();
    }

    public function getMunicipalities($data) {
        return $this->citymunicipalities->where('provCode', $data['province_code'])->get();
    }

    public function getBarangays($data) {
        return $this->barangays->where('citymunCode', $data['municipality_code'])->get();
    }
}