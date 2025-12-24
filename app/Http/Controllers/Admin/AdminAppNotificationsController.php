<?php

namespace App\Http\Controllers\Admin;

use App\Events\AppNotificationCreated;
use App\Events\AppNotificationsBulkChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Notifications\StoreAdminAppNotificationRequest;
use App\Http\Requests\Admin\Notifications\UpdateAdminAppNotificationRequest;
use App\Models\AppNotification;
use App\Models\User;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AdminAppNotificationsController extends Controller
{
    public function __construct(
        private DataTableService $dataTable
    ) {
        $this->middleware('permission:manage-notifications');
    }


    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: AppNotification::query()
                ->select([
                    'id',
                    'user_id',
                    'scope',
                    'type',
                    'title',
                    'message',
                    'scheduled_on',
                    'auto_expire_on',
                    'sent_as_scheduled',
                    'created_at',
                ])
                ->withCount([
                    'reads as read_count' => function ($q) {
                        $q->whereNotNull('read_at');
                    },
                    'reads as dismissed_count' => function ($q) {
                        $q->whereNotNull('dismissed_at');
                    },
                    'reads as deleted_count' => function ($q) {
                        $q->whereNotNull('u_del_notif_at');
                    },
                ])
                ->with(['user:id,name,email']),
            request: $request,
            config: [
                'searchable' => ['title', 'message', 'user.name', 'user.email'],
                'sortable' => [
                    'title' => ['type' => 'simple'],
                    'type' => ['type' => 'simple'],
                    'created_at' => ['type' => 'simple'],
                ],
                'resource' => 'notifications',
                'transform' => function ($item) {
                    $item->created_at_diff = $item->created_at?->diffForHumans();
                    $item->scheduled_on_diff = $item->scheduled_on?->diffForHumans();
                    $item->auto_expire_on_diff = $item->auto_expire_on?->diffForHumans();
                    $item->username = $item->user?->name;
                    $item->user_email = $item->user?->email;

                    return $item;
                },
            ]
        );

        return Inertia::render('Admin/Notifications/AdminNotificationsIndex', [
            'notifications' => $result['data'],
            'filters'       => $result['filters'],
        ]);
    }


    public function create()
    {
        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->limit(250)
            ->get();

        return Inertia::render('Admin/Notifications/AdminCreateNotificaion', [
            'users' => $users,
        ]);
    }


    public function store(StoreAdminAppNotificationRequest $request)
    {
        $data = $request->validated();

        $scope = in_array($data['scope'] ?? 'user', ['user', 'system', 'release'], true)
            ? $data['scope']
            : 'user';

        $userId = $scope === 'user' ? ($data['user_id'] ?? null) : null;

        $scheduledOn = !empty($data['scheduled_on']) ? Carbon::parse($data['scheduled_on']) : null;
        $autoExpireOn = !empty($data['auto_expire_on']) ? Carbon::parse($data['auto_expire_on']) : null;

        $notification = AppNotification::create([
            'user_id'           => $userId,
            'scope'             => $scope,
            'type'              => $data['type'],
            'title'             => $data['title'],
            'message'           => $data['message'],
            'data'              => null,
            'scheduled_on'      => $scheduledOn,
            'auto_expire_on'    => $autoExpireOn,
            'sent_as_scheduled' => false,
        ]);

        if (empty($scheduledOn) || $scheduledOn->isPast() || $scheduledOn->lessThanOrEqualTo(now())) {
            event(new AppNotificationCreated($notification));
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created.');
    }


    public function edit(string $id)
    {
        $notification = AppNotification::query()->whereKey($id)->firstOrFail();

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->limit(250)
            ->get();

        return Inertia::render('Admin/Notifications/AdminEditNotificaion', [
            'notification' => $notification,
            'users'        => $users,
        ]);
    }


    public function update(UpdateAdminAppNotificationRequest $request, string $id)
    {
        $notification = AppNotification::query()->whereKey($id)->firstOrFail();
        $data = $request->validated();

        $scope = in_array($data['scope'] ?? 'user', ['user', 'system', 'release'], true)
            ? $data['scope']
            : 'user';

        $userId = $scope === 'user' ? ($data['user_id'] ?? null) : null;

        $scheduledOn = !empty($data['scheduled_on']) ? Carbon::parse($data['scheduled_on']) : null;
        $autoExpireOn = !empty($data['auto_expire_on']) ? Carbon::parse($data['auto_expire_on']) : null;

        $notification->forceFill([
            'user_id'        => $userId,
            'scope'          => $scope,
            'type'           => $data['type'],
            'title'          => $data['title'],
            'message'        => $data['message'],
            'data'           => null,
            'scheduled_on'   => $scheduledOn,
            'auto_expire_on' => $autoExpireOn,
        ])->save();

        $broadcastTarget = $notification->user_id ?: 'system';
        event(new AppNotificationsBulkChanged($broadcastTarget, 'bulk', [$notification->id]));

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated.');
    }


    public function destroy(string $id)
    {
        $notification = AppNotification::query()->whereKey($id)->firstOrFail();

        $ids = [$notification->id];
        $broadcastTarget = $notification->user_id ?: 'system';

        $notification->delete();

        event(new AppNotificationsBulkChanged($broadcastTarget, 'bulk', $ids));

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted.');
    }


    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['string'],
        ]);

        $ids = array_values(array_unique(array_filter($data['ids'])));

        if (!count($ids)) {
            return redirect()->route('admin.notifications.index');
        }

        $items = AppNotification::query()
            ->select(['id', 'user_id'])
            ->whereIn('id', $ids)
            ->get();

        $idsByTarget = [];

        foreach ($items as $item) {
            $target = $item->user_id ?: 'system';
            $idsByTarget[$target] ??= [];
            $idsByTarget[$target][] = $item->id;
        }

        AppNotification::query()->whereIn('id', $items->pluck('id')->all())->delete();

        foreach ($idsByTarget as $target => $targetIds) {
            event(new AppNotificationsBulkChanged((string) $target, 'bulk', array_values($targetIds)));
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifications deleted.');
    }


    public function deleted(Request $request)
    {
        return Inertia::render('Admin/Notifications/AdminDeletedNotificaionsIndex');
    }
}
