<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\MedicalCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DoctorController extends Controller
{




    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::all();
        // $doctors = [];
        // foreach ($users as $user) {
        //     if($user->doctor != null)
        //     {
        //         $doctors[] = $user;
        //     }
        // }
        // return response()->json([
        //     'status'    => 'success',
        //     'doctors'  => $doctors
        // ]);

        Log::info("reached doctors api end-point");
        return response([
            'doctors' => Doctor::orderBy('created_at', 'desc')->with('user:id,first_name,last_name,email,phone')
            ->get()
        ], 200);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    // Read medical data pdf file
    public function readPdf(Request $request)
    {

        // Log::info($request->all());
        $validated_data = request()->validate([
            'unique_token' => 'required|string',
        ]);

        $medicalCard = MedicalCard::where('unique_token', $validated_data['unique_token'])->first();

        if(!empty($medicalCard))
        {
            // $pdfUrl = "";
            return response()->json([

                'status' => 'success',
                'medical_card_pdf' => $medicalCard->medical_card_pdf,

            ], 200);
        }

        return response()->json([

            'error' => 'Aucune correspondance trouvée!',
            'medical_card_pdf' => "",
            'message' => 'Aucune correspondance trouvée!'

        ],403);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // print_r("hi");
        // $doctor_code = "";
        // $id = (int)$id;
        // $request->merge(["user_id" => $id]);
        // $id_type = gettype($id);
        // return response([
        //     'status' => 'success',
        //     "request" => $request->all()], 200);



        $validated_data = $request->validate([
            'type' => "required|string",
            'gender' => "required|string",
            'hospital' => "required|string",
            'job_title' => "required|string",
            'address' => "required|string",
            'id_card' => "required|string",
            'doctor_card' => "required|string",
            'first_name' => "required|String|min:3|max:255",
            'last_name' => "required|String|min:3|max:255",
            'phone' => "required|string|min:8|max:8"
        ]);


        // random password
        // $random_password = Hash::make(Str::random(9));
        $random_password = Hash::make("password");
        $new_user = User::create([
            'first_name' => $validated_data['first_name'],
            'last_name' => $validated_data['last_name'],
            'phone' => $validated_data['phone'],
            'address' => $validated_data['address'],
            'password' => $random_password
        ]);
        // return response([
        //     'status' => 'success',
        //     "request" => $request->all()], 200);



        //AUTOGENERATED [ Identifiant medecin (DOC + Initiale Prenom
        // + initiale nom + CODE autogenerated + Initiale Genre) ]
        // $doctor_first_name_initial = substr();

        if (!is_null($new_user)) {

            $random_doctor_code = rand(100000,999999);

            $doctor_user = $new_user;
            $doctor_code = "DOC-".substr($doctor_user->first_name,0,1)
                                 .substr($doctor_user->last_name,0,1)
                                 .substr($doctor_user->first_name,0,1)
                                 .$random_doctor_code
                                 .substr($doctor_user->gender,0,1);

            $doctor_code = strtoupper($doctor_code);

            $new_doctor = Doctor::create([
                'user_id' => $doctor_user->id,
                'type' => $validated_data['type'],
                'code' => $doctor_code,
                'gender' => $validated_data['gender'],
                'hospital' => $validated_data['hospital'],
                'job_title' => $validated_data['job_title'],
                'id_card' => $validated_data['id_card'],
                'doctor_card' => $validated_data['doctor_card'],
            ]);
        }

        if(!is_null($new_doctor)) {
            return response([
                'status' => 'success',
                "doctor_user" => $doctor_user,
                "doctor_info" => $new_doctor], 200);
        }

        return response()->json(["message" => "Un problème est survenu lors de l'enregistrement"], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $request = new Request();
        $id = (int)$id;
        $request->merge(['id' => $id]); //add request

        $validated_data = $request->validate([
            'id' => "required|numeric"
        ]);


        $user = User::find($validated_data['id']);
        $doctor = User::find($validated_data['id'])->doctor;

        if(!is_null($user && $doctor))
        {
            return response()->json([
                'status' => 'success',
                "doctor_user" => $user,
                "doctor_info" => $doctor
            ], 200);
        }

        return response()->json([
            'status' => 'not found',
        ], 204);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $request = new Request();
        $id = (int)$id;
        $request->merge(['user_id' => $id]); //add request

        $validated_data = $request->validate([
            'user_id' => "required|numeric",
            'type' => "required|string",
            'gender' => "required|string",
            'hospital' => "required|string",
            'id_card' => "required|string",
            'doctor_card' => "required|string",
            // 'id_card' => "required|image|size:1024||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000",
            // 'doctor_card' => "required|image|size:1024||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000",
        ]);

        $user = User::find($validated_data['user_id']);
        if(!is_null($user))
        {
            $doctor = Doctor::where('user_id', $user->id)->first();

            $doctor->update([
                'user_id' => $request->user_id,
                'type' => $validated_data['type'],
                'gender' => $validated_data['gender'],
                'hospital' => $validated_data['hospital'],
                'id_card' => $validated_data['id_card'],
                'doctor_card' => $validated_data['doctor_card'],
                ]);

            return response()->json([
                                    'status' => 'success',
                                    "doctor_user" => $user,
                                    "doctor_info" => $doctor
                                    ], 200);
        }

        return response()->json(["message" => "Aucun compte trouvé !"], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $request = new Request();
        $id = (int)$id;
        $request->merge(['id' => $id]); //add request

        $validated_data = $request->validate([
            'id' => "required|numeric",
        ]);

        $user = User::find($validated_data['id']);

        if(!is_null($user))
        {
            $doctor = $user->doctor;
            $doctor->status = false;
            $doctor->save();


        // return response([
        //     'doctor' => $doctor,
        //     "request" => $request->all()], 200);
            return response()->json([
                                        'status' => 'success',
                                        'doctor' => $doctor,
                                    ], 200);
        }
        return response()->json(["message" => "Un problème est survenu lors de l'enregistrement"], 404);

    }
}
