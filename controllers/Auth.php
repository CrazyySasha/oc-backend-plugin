<?php namespace Crazy\Backend\Controllers;


use App;
use ApplicationException;
use Backend;
use Backend\Models\AccessLog;
use Backend\Models\User as UserModel;
use Backend\Models\UserRole;
use BackendAuth;
use Config;
use Exception;
use Flash;
use Mail;
use Request;
use System;
use System\Classes\UpdateManager;
use ValidationException;
use Validator;

/**
 * Auth Backend Controller
 */
class Auth extends \Backend\Controllers\Auth
{


    /**
     * __construct is the constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->publicActions[] = "signup";
    }


    public function signup()
    {
        $this->bodyClass = 'setup';

        try {
            if ($this->checkPostbackFlag()) {
                return $this->handleSubmitSignup();
            }
        }
        catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }

    /**
     * handleSubmitSignup creates a new admin
     */
    protected function handleSubmitSignup()
    {

        // Validate user input
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|between:6,255|email|unique:backend_users',
            'login' => 'required|between:2,255|unique:backend_users',
            'password' => 'required:create|between:4,255|confirmed',
            'password_confirmation' => 'required_with:password|between:4,255'
        ];
        $validation = Validator::make(post(), $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        // Look up default role
        $roleId = UserRole::where('code', UserRole::CODE_PUBLISHER)->first()->id ?? null;


        // Create user and sign in
        $user = new UserModel;
        $user->forceFill([
            'last_name'             => array_get(post(), 'last_name'),
            'first_name'            => array_get(post(), 'first_name'),
            'email'                 => array_get(post(), 'email'),
            'login'                 => array_get(post(), 'login'),
            'password'              => array_get(post(), 'password'),
            'password_confirmation' => array_get(post(), 'password_confirmation'),
            'permissions'           => [],
            'role_id' => $roleId,
            "is_superuser" => false,
            'is_activated' => false,
        ]);
        $user->save();
        
        BackendAuth::login($user);

        // Redirect
        Flash::success('Welcome to your Administration Area, '.post('first_name'));
        return Backend::redirect('backend');
    }
}
