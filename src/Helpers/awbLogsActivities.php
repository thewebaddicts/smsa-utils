<?php

if (!function_exists('get_operator_info')) {
    function get_operator_info($user)
    {
        $employeeId = $user->employee_id ?? $user->id ?? '';
        $name = $user->name ?? '';
        return $employeeId . ' | ' . $name;
    }
}



if (!function_exists('get_branch_info')) {
    function get_branch_info($hub)
    {
        $branch_identifier = $hub->identifier ?? '';
        $branchName = $hub->label ?? '';
        return $branchName . ' | ' . $branch_identifier;
    }
}