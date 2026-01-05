<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'jilla_id',
        'gram_id',
        'vibhag_id',
        'mobile_no',
        'role',
        'address',
        'status',
        'prant_id',
        'vibhag_id'
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getUserByToken($token)
    {
        $users = DB::table('oauth_access_tokens')
            ->join('users as u', 'u.id', 'oauth_access_tokens.user_id')
            ->where(['oauth_access_tokens.id' => $token])
            ->where(function ($q) {
                $q->where('u.role', '=', '2')
                    ->orWhere('u.role', '=', '6');
            })
            ->first();
        return $users;
    }

    public function users()
    {
        return $this->belongsTo(MedicineRequest::class, 'arogyamitra_id');
    }

    public function gram()
    {
        return $this->hasOne(Gram::class, 'id');
    }

    public static function getArogyaMitraIds($startDate, $endDate, $role, $prantId, $vibhagId, $jillaId)
    {
        $beneficiary = User::select(DB::raw("SUM(b.number_of_beneficiary) AS beneficiary"))
            ->join('gram as g', 'g.id', 'users.gram_id')
            ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
            ->join('taluka as t', 't.id', 'gj.taluka_id')
            ->join('jilla as j', 'j.id', 't.jilla_id')
            ->join('vibhag as v', 'v.id', 'j.vibhag_id')
            ->join('beneficiaries as b', 'b.gram_id', 'users.gram_id');
        if ($role == 4) { // vibhag user
            $beneficiary->where(['j.vibhag_id' => $vibhagId, 't.jilla_id' => $jillaId]);
        } elseif ($role == 5) {
            $beneficiary->join('prant as p', 'p.id', 'v.prant_id')->where(['v.prant_id' => $prantId, 'j.vibhag_id' => $vibhagId]);
        } else {
            $beneficiary->join('prant as p', 'p.id', 'v.prant_id')->where(['p.id' => $prantId]);
        }
        if ($startDate && $endDate) {
            $beneficiary->whereBetween('b.created_at', [date('Y-m-d', strtotime($startDate)) . " 00:00:00", date('Y-m-d', strtotime($endDate)) . " 23:59:59"]);
        }
        $beneficiary = $beneficiary->where('users.role', '3')->first();

        return $beneficiary ? (int)$beneficiary->beneficiary : 0;
    }

    public static function getArogyaMitraIdsByFilterType($type, $startDate, $endDate, $prantId, $vibhagId, $jillaId, $taluka, $gramjuth, $gram)
    {
        $query = User::select(DB::raw("SUM(b.number_of_beneficiary) AS beneficiary"))
            ->join('gram as g', 'g.id', 'users.gram_id')
            ->join('gramjuth as gj', 'gj.id', 'g.gramjuth_id')
            ->join('beneficiaries as b', 'b.gram_id', 'users.gram_id');
        if ($type == 'vibhag') {
            $query->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 't.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id')
                ->join('prant as p', 'p.id', 'v.prant_id');
        } elseif ($type == 'jilla') {
            $query->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 't.jilla_id')
                ->join('vibhag as v', 'v.id', 'j.vibhag_id');
        } elseif ($type == 'taluka') {
            $query->join('taluka as t', 't.id', 'gj.taluka_id')
                ->join('jilla as j', 'j.id', 't.jilla_id');
        } elseif ($type == 'gramjuth') {
            $query->join('taluka as t', 't.id', 'gj.taluka_id');
        }
        if ($type == 'vibhag') {
            $query->where(['v.prant_id' => $prantId, 'j.vibhag_id' => $vibhagId]);
        } elseif ($type == 'jilla') {
            $query->where(['j.vibhag_id' => $vibhagId, 't.jilla_id' => $jillaId]);
        } elseif ($type == 'taluka') {
            $query->where(['t.jilla_id' => $jillaId, 'gj.taluka_id' => $taluka]);
        } elseif ($type == 'gramjuth') {
            $query->where(['gj.taluka_id' => $taluka, 'g.gramjuth_id' => $gramjuth]);
        } else {
            $query->where(['g.gramjuth_id' => $gramjuth, 'users.gram_id' => $gram]);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('b.created_at', [date('Y-m-d', strtotime($startDate)) . " 00:00:00", date('Y-m-d', strtotime($endDate)) . " 23:59:59"]);
        }
        $query = $query->where('users.role', '3')->first();

        return $query ? (int)$query->beneficiary : 0;
    }

    public static function dateDiffInDays($startDate, $endDate)
    {
        $diff = strtotime($endDate) - strtotime($startDate);
        return abs(round($diff / (60 * 60 * 24)));
    }
}
