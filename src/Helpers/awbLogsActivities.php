<?php

if (!function_exists('get_operator_info')) {
    function get_operator_info($user)
    {
        $employeeId = $user->employee_id ?? $user->id ?? '';
        $name = $user->name ?? '';
        return $employeeId . ' | ' . $name;
    }
}

if (!function_exists('get_guest_info')) {
    function get_guest_info($user)
    {
        $id = $user->id;
        $name = $user->name ?? '';
        return $id . ' | ' . $name;
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

  if (!function_exists('get_user_info')) {
        function get_user_info($user)
        {
            $id = $user->id;
            $name = $user->first_name . ' ' . $user->last_name;
            return $id . ' | ' . $name;
        }
    }