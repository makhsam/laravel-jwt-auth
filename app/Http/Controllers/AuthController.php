<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AuthController extends Controller
{
    /**
     * @param user_id
     * @param password
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'password' => 'required|min:6'
        ]);

        try { // Find the user by ID
            $user = User::findOrFail($request->input('user_id'));
        }
        catch(ModelNotFoundException $e) {
            return api_response('not_valid', 'Неверное логин или пароль');
        }

        // Verify the password and generate the token
        if (Hash::check($request->input('password'), $user->password)) {
            
            // Update verified_at after first time successful login
            $user->verifyAccount();

            return api_response('done', 'Вы успешно вошли в систему', [
                'user' => $user->load('role.permissions'), // appends roles & permissions
                'token' => $user->generateToken(),
            ]);
        }

        // Bad request
        return api_response('not_valid', 'Неверное логин или пароль');
    }


    /**
     * @param phone_number 
     * @param first_name 
     * @param last_name 
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required'
            'password' => 'required'
        ]);

        if (User::where('phone_number', $request->input('phone_number'))->exists()) {
            return api_response('not_valid', 'Номер телефона уже зарегистрирован', null, -2801);
        }

        // GENERATE PASSWORD
        $password = $request->input('password');

        // CREATE NEW USER
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'password' => bcrypt($password)
        ]);

        // Generate SMS message content
        $msgContent = $this->prepareMessageContent($user->id, $password);

        // SEND SMS MESSAGE
        return $this->sendMessage($user->phone_number, $msgContent);
    }


    /**
     * Send user ID and password via SMS
     */
    protected function sendMessage($phone, $content)
    {
        if ( !Str::startsWith($phone, '+998')) { 
            return api_response('done', 'Иностранный номер используется. Пожалуйста, свяжитесь с администратором');
        }

        // Prepare CURL input array
        $data = array([
            'phone' => ltrim($phone, '+'),
            'text' => $content
        ]);

        // Initialize CURL for sending SMS
        $ch = curl_init("http://83.69.139.182:8080/");
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, "login=compass&password=lTgsQPQCpxaKG8JTrDEc&data=" . json_encode($data));
    
        $output = curl_exec($ch);
        curl_close($ch);
    
        // Save output in file
        $this->saveMessageDetails($phone, $output);

        $result = json_decode($output);
        if (empty($result) || isset($result[0]->error)) { 
            // Error occured while sending SMS 
            return api_response('not_valid', "Номер телефона {$phone} не существует");
        }

        // Successfully sent.
        return api_response('done', 'ID пользователя и пароль отправлен через SMS');
    }


    /**
     * Prepare message
     */
    protected function prepareMessageContent($user_id, $password) {
        return sprintf(
            "www.bctraining.uz\r\nID: %d\r\nParol: %d",
            $user_id, $password
        );
    }


    /**
     * Store sent message details
     */
    protected function saveMessageDetails($phone, $content)
    {
        $phone_number = ltrim($phone, '+'); // removes '+' in phone number
        $filePath = storage_path("app/messages/log.txt");

        return file_put_contents($filePath, $content."\r\n", FILE_APPEND | LOCK_EX);
    }

}
