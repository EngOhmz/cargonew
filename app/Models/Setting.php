<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 20/4/20
 * Time: 2:47 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['site_name','site_email','site_phone_number','site_footer','timezone','site_logo','site_description','site_address','locale','twilio_auth_token','twilio_account_sid','twilio_from','twilio_disabled','mail_host','mail_port','mail_username','mail_password','mail_from_name','mail_from_address','mail_disabled','notifications_email','notifications_sms','notify_templates','invite_templates'];
}