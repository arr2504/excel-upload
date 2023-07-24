<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Call the student data stored by the user
        $data_students = Student::whereUserId(Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return response([
            "status" => "Success",
            "message" => "Data student successfully fetch",
            "data" => [
                "students" => $data_students,
            ]
        ]);
    }

    /**
     * Upload file to temp get the data and store
     */
    public function upload(Request $request)
    {

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Temp path file
            $path = $request->file('file')->getRealPath();

            // Read the Excel file data (skipping the header)
            $excel_rows = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX)[0];

            // Remove the header row
            array_shift($excel_rows);

            // For success counter
            $success_rows = 0;

            // Store data new student record
            $addStudent = [];
            foreach ($excel_rows as $excel_row) {

                // Group data
                $student_data = [
                    'user_id' => Auth::user()->id,
                    'name' => $excel_row[0],
                    'classroom_id' => $excel_row[1],
                    'level' => $excel_row[2],
                    'parent_contact' => $excel_row[3],
                ];

                // Check classroom id
                $classroom_id = Classroom::where('name', 'LIKE', '%' . $student_data['classroom_id'] . '%')->first();

                // If classroom exist
                if ($classroom_id) {
                    // Set classroom_id array data with id classroom
                    $student_data['classroom_id'] = $classroom_id->id;
                } else {
                    // If classroom not exist create new
                    $new_classroom_id = Classroom::create([
                        'name' => $student_data['classroom_id'],
                    ]);
                    // Assign classroom_id array data with new id classroom
                    $student_data['classroom_id'] = $new_classroom_id->id;
                }

                // Check if the student record already exists
                $existing_student = Student::where('name', $student_data['name'])
                    ->where('classroom_id', $student_data['classroom_id'])
                    ->where('level', $student_data['level'])
                    ->where('parent_contact', $student_data['parent_contact'])
                    ->exists();

                // If data student not exist
                if (!$existing_student) {
                    // Create a new student record
                    $addStudent[] = Student::create($student_data);
                    $success_rows++;
                }
            }

            // Count all rows
            $totalCount = count($excel_rows);

            return response([
                "status" => "Success",
                "message" => "Data student successfully store",
                "data" => [
                    "total_rows" => $totalCount,
                    "success_rows" => $success_rows,
                    "students" => $addStudent,
                ]
            ]);
        }

        return response([
            "status" => "Failed",
            "message" => "File is missing",
        ]);
    }

    /**
     * Download the specified resource in storage.
     */
    public function download()
    {
        $filePath = storage_path('app/public/template-students.xlsx'); // Replace with the actual file path in the storage folder

        // Check if the file exists
        if (file_exists($filePath)) {
            // Get the file contents
            $fileContents = file_get_contents($filePath);

            // Return the file as a download response
            return response($fileContents)
                ->header('Content-Type', 'application/xlsx')
                ->header('Content-Disposition', 'attachment; filename="template-students.xlsx"'); // Replace with the desired file name and extension
        }

        // Return a response if the file is not found
        return response()->json(['message' => 'File not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
