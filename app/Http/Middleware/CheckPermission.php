<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckPermission
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permissionName): Response
    {
        // ១. យក User ដែលកំពុង Login តាមរយៈ JWT Token
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized or Token Expired'], 401);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // ករណីលើកលែង៖ ប្រសិនបើជា Super Admin (ឧទាហរណ៍ role_id = 1) អនុញ្ញាតឱ្យឆ្លងកាត់ដោយស្វ័យប្រវត្តិ
        // $isAdmin = DB::table('user_roles')->where('user_id', $user->id)->where('role_id', 1)->exists();
        // if ($isAdmin) return $next($request);

        // ២. ប្រើប្រាស់ SQL ដដែល ដើម្បីឆែកមើលថា តើ User នេះមានសិទ្ធិដែលយើងកំពុងទាមទារឬអត់?
        // យើងទាមទារសិទ្ធិអ្វីមួយ (ឧ. 'Product.Add') តាមរយៈអថេរ $permissionName
        $hasPermission = DB::selectOne('
            SELECT p.id
            FROM permissions p
            INNER JOIN user_permission_roles pr ON p.id = pr.permission_id
            INNER JOIN roles r ON pr.role_id = r.id
            INNER JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ? AND p.name = ?
            LIMIT 1
        ', [$user->id, $permissionName]);

        // ៣. បើរកមិនឃើញសិទ្ធិនោះទេ ទាត់គាត់ចេញ! (403 Forbidden)
        if (!$hasPermission) {
            return response()->json([
                'status' => 'error',
                'message' => "Access Denied! You don't have permission: [{$permissionName}]"
            ], 403);
        }

        // ៤. បើមានសិទ្ធិ អនុញ្ញាតឱ្យគាត់ទៅកាន់ Controller បន្តទៀត
        return $next($request);
    }
}