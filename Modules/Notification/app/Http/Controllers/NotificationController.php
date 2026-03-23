<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Notification\Models\Notification;
use Modules\Notification\Services\OneSignalService;
use Illuminate\Support\Facades\Storage;
class NotificationController extends Controller
{
    protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

    public function sendPushNotificationToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'extra_data'=> 'nullable|array',
        ]);
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'notification_image' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('Notifications', $fileName, 'public');
            $image = Storage::url($path);
        }

        $image = $image ?? null;
        $extraData = $request->extra_data ?? null;

        $response = $this->oneSignalService->sendNotificationToAll(
            $request->title,
            $request->message,
            $image,
            $extraData
        );
        return $this->respondOk($response);
    }

    public function notifyUser(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'recipientIds' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'extra_data'=> 'nullable|array',
        ]);
        $recipientIds = $request->recipientIds;
        $title =  $request->title;
        $message =  $request->message;
        $extraData = $request->extra_data ?? null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'notification_image' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('Notifications', $fileName, 'public');
            $image = Storage::url($path);
        }

        $image = $image ?? null;
        $response = $this->oneSignalService->sendNotificationToUser($recipientIds, $title, $message, $image,$extraData);

        return $this->respondOk($response, 'Notifications sent successfully');
    }

    public function getAllNotifications(Request $request)
    {
        $user=auth('sanctum')->user();
        $notificationQuery = Notification::query();

        if($user->hasRole('Admin')) {
            if($request->has('recipent_id')) {
                $notificationQuery->where(function ($query) use ($request) {
                    $query->where('recipient_id', $request->recipent_id)
                        ->orWhereNull('recipient_id');
                });
            }
        } else {
            $notificationQuery->where(function ($query) use ($user) {
                $query->where('recipient_id', $user->id)
                    ->orWhereNull('recipient_id');
            });

            Notification::where('recipient_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $notifications = $notificationQuery->paginate();
        return $this->respondOk($notifications, 'Notifications retrieved successfully');
    }

    public function deleteNotifications(Request $request)
    {
        $user = auth('sanctum')->user();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:notifications,id'
        ]);

        $ids = $request->ids;

        if ($user->hasRole('Admin')) {
            Notification::whereIn('id', $ids)->delete();
            return $this->respondOk(null, "Notifications deleted successfully by SuperAdmin.");
        }

        Notification::whereIn('id', $ids)
            ->where('recipient_id', $user->id)
            ->delete();

        return $this->respondOk([], "Your notifications were deleted successfully.");
    }

    public function getCountUnReadedNotifications()
    {
        $user = auth('sanctum')->user();
        $unreadCount = Notification::where(function ($query) use ($user) {
            $query->where('recipient_id', $user->id)
                ->orWhereNull('recipient_id');
        })->where('is_read', false)->count();

        return $this->respondOk($unreadCount, "Unread notifications count retrieved successfully.");
    }

}
