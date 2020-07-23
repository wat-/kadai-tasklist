<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();

            // ユーザの投稿一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->get();
            // メッセージ一覧ビューでそれを表示
            return view('tasks.index', [
                'user' => $user,
                'tasks' => $tasks,
            ]);
        } else {
            // 認証済みでない場合は空文字を返却
            return view('tasks.index', [
                'tasks' => '',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);

        // メッセージを作成
        $task = new Task;
        $task->status = $request->status;
        $task->content = $request->content;
        $task->user_id = \Auth::id();
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        
        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
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
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
       // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        
        // ログインしたユーザのみ操作可能
        if (\Auth::id() === $task->user_id) {
            // メッセージを更新
            $task->status = $request->status;
            $task->content = $request->content;
            $task->save();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        
        // ログインしたユーザのみ操作可能
        if (\Auth::id() === $task->user_id) {
            // メッセージを削除
            $task->delete();
        }
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
