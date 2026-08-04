<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

class NotificationsController extends Controller
{
    public function poll(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'unreadCount' => 0,
                'items' => [],
            ]);
        }

        $unread = $user->unreadNotifications()
            ->latest()
            ->limit(12)
            ->get();

        $items = $unread->map(function ($n) {
            $data = $n->data ?? [];

            $title = $data['title'] ?? 'Notificación';
            $msg   = $data['message'] ?? '';
            $url   = '#';

            // ✅ Si viene URL directa
            if (!empty($data['url'])) {
                $url = $data['url'];
            } else {
                // ✅ Si viene routeName / routeParams, sólo si existe (evita RouteNotFoundException)
                $rName   = $data['routeName'] ?? null;
                $rParams = $data['routeParams'] ?? [];

                if ($rName && RouteFacade::has($rName)) {
                    $url = route($rName, $rParams);
                } else {
                    // ✅ fallback seguro
                    $url = url('/agenda');
                }
            }

            return [
                'id'      => $n->id,
                'title'   => $title,
                'message' => $msg,
                'time'    => $n->created_at?->diffForHumans(),
                'url'     => $url,
                'type'    => $data['type'] ?? null,
            ];
        })->values();

        return response()->json([
            'unreadCount' => $user->unreadNotifications()->count(),
            'items'       => $items,
        ]);
    }

    public function read(Request $request)
    {
        $request->validate([
            'id' => ['required', 'string'],
        ]);

        $user = $request->user();
        $n = $user?->unreadNotifications()->where('id', $request->id)->first();

        if ($n) {
            $n->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function readAll(Request $request)
    {
        $user = $request->user();
        $user?->unreadNotifications->markAsRead();

        return back();
    }
}
