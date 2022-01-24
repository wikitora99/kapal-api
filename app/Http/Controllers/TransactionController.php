<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Models\Transaction;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Transaction::all();
        $in = $data->where('type', 'income')->sum('amount');
        $out = $data->where('type', 'outcome')->sum('amount');

        $response = [
            'message' => 'List of Data Transaction',
            'total data' => $data->count(),
            'total income' => $in,
            'total outcome' => $out,
            'total revenue' => $in - $out,
            'all data' => $data
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => ['required'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'in:income,outcome']
        ]);

        if ($valid->fails()){
            return response()->json($valid->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data = Transaction::create($request->all());

            $response = [
                'message' => 'New Data Transaction Has Been Added',
                'data' => $data
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed : '.$e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Transaction::findOrFail($id);

        $response = [
            'message' => 'Data Transaction by ID = '.$id,
            'data' => $data
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = Transaction::findOrFail($id);

        $valid = Validator::make($request->all(), [
            'amount' => ['numeric'],
            'type' => ['in:income,outcome']
        ]);

        if ($valid->fails()){
            return response()->json($valid->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data->update($request->all());

            $response = [
                'message' => 'Data Transaction With id '.$id.' Has Been Updated',
                'data' => $data
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed : '.$e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Transaction::findOrFail($id);

        try {
            $data->delete();

            $response = [
                'message' => 'Data Transaction With id '.$id.' Has Been Deleted'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed : '.$e->errorInfo
            ]);
        }      
    }
}
