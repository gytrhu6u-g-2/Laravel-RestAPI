<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{

    /**
     * Studentsを取得
     * @return json
     */
    public function index()
    {
        $students = Student::all();

        // studentsが0以上か
        if ($students->count() > 0) {
            // 結果をjson形式で返す
            return response()->json([
                'status' => 202,
                'students' => $students
            ], 202);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Records Found'
            ], 404);
        }
    }

    /**
     * 登録処理
     * @param request
     * @return json
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'course' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|digits:10',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validated->errors(),
            ], 422);
        } else {
            DB::begintransaction();
            try {
                Student::create([
                    'name' => $request->name,
                    'course' => $request->course,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => 'something Went Wrong!'
                ],);
            }

            return response()->json([
                'status' => 202,
                'message' => 'Student Created Successfully'
            ],);
        }
    }

    /**
     * id情報の表示
     * @param id
     * @return json
     */
    public function show($id)
    {
        $student = Student::find($id);

        if ($student) {
            return response()->json([
                'status' => 202,
                'student' => $student
            ],);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Not Found Such an ID'
            ], 404);
        }
    }

    /**
     * 更新
     * @param request id
     * @return json
     */
    public function update(Request $request, int $id)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'course' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|digits:10',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validated->errors(),
            ], 422);
        } else {
            $student = Student::find($id);

            if ($student) {
                DB::begintransaction();
                try {
                    $student->fill([
                        'name' => $request->name,
                        'course' => $request->course,
                        'email' => $request->email,
                        'phone' => $request->phone,
                    ]);
                    $student->save();
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 500,
                        'message' => 'something Went Wrong!'
                    ],);
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Not Found Such an ID'
                ], 404);
            }

            return response()->json([
                'status' => 202,
                'message' => 'Student Updated Successfully'
            ],);
        }
    }

    /**
     * 削除
     * @param id
     * @return json
     */
    public function delete($id)
    {
        $student = Student::find($id);

        if ($student) {
            Student::destroy($id);

            return response()->json([
                'status' => 202,
                'message' => 'Student Deleted Successfully'
            ],);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Not Found Such an ID'
            ], 404);
        }
    }


    /**
     * 検索
     * @param request
     * @return json
     */
    public function search(Request $request)
    {
        $data = $request->all();

        $results = DB::table('students')
            ->select('*')
            ->where(function ($query) use ($data) {
                if (isset($data['name']) &&  $data['name'] !== null) {
                    $query->where('name', 'like', '%' . $data['name'] . '%');
                }
            })
            ->where(function ($query) use ($data) {
                if (isset($data['course']) &&  $data['course'] !== null) {
                    $query->where('course', 'like', '%' . $data['course'] . '%');
                }
            })
            ->where(function ($query) use ($data) {
                if (isset($data['email']) &&  $data['email'] !== null) {
                    $query->where('email', 'like', '%' . $data['email'] . '%');
                }
            })
            ->where(function ($query) use ($data) {
                if (isset($data['phone']) &&  $data['phone'] !== null) {
                    $query->where('phone', 'like', '%' . $data['phone'] . '%');
                }
            })
            ->orderBy('students.id', 'ASC')
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Data Not Found'
            ], 404);
        }

        return response()->json([
            'status' => 202,
            'student' => $results
        ], 202);
    }
}
