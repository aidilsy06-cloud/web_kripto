<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Credential;
use App\Models\Report;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCredentials = Credential::count();
        $totalReportsPending = Report::where('status', 'pending')->count();
        $totalReportsResolved = Report::where('status', 'resolved')->count();

        // Get all users along with their count of credentials and reports
        $users = User::withCount(['credentials', 'reports'])->latest()->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCredentials',
            'totalReportsPending',
            'totalReportsResolved',
            'users'
        ));
    }

    public function reports()
    {
        $reports = Report::with('user')->latest()->get();
        return view('admin.reports.index', compact('reports'));
    }

    public function showReport(Report $report)
    {
        $report->load('user');
        return view('admin.reports.show', compact('report'));
    }

    public function replyReport(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,resolved',
            'admin_reply' => 'nullable|string',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_reply' => $request->admin_reply,
        ]);

        return redirect()->route('admin.reports.show', $report->id)->with('success', 'Balasan berhasil dikirim dan status laporan diperbarui.');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin',
        ]);

        $salt = base64_encode(random_bytes(24));

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'master_key_salt' => $salt,
            'role' => $request->role,
            'is_verified' => true,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Akun pengguna baru berhasil ditambahkan.');
    }

    public function destroyUser(User $user)
    {
        // Prevent admin from deleting themselves
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Akun pengguna berhasil dihapus beserta seluruh kredensial dan laporannya.');
    }
}
