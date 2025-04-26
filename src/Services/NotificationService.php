<?php

namespace Thadico\FcmNotification\Services;

use App\Helpers\FcmHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use MongoDB\BSON\UTCDateTime;

/**
 * @Author khaidv
 * @Date DummyDate
 */
class NotificationService
{
    private const MONGO_ID = '_id';
    private const OTHER_ID = 'id';
    private string $mongodb = 'mongodb';
    private string $primaryKey = 'id';

    /**
     * @param Model $model
     * @param ?string $alias
     */
    public function __construct( private readonly Model $model) {
        $this->primaryKey = $this->getPrimaryKey(config('database.default', 'mongodb'));
    }

    /**
     * Get primary key.
     *
     * @param string $database
     *
     * @return string
     */
    public function getPrimaryKey(string $database = 'mongodb'): string
    {
        return match ($database) {
            $this->mongodb => self::MONGO_ID,
            default => self::OTHER_ID
        };
    }

    /**
     * Push notification.
     *
     * @param string $type
     * @param string $content
     * @param array $userIds
     *
     * @return bool
     */
    public function push(string $type, string $content, array $userIds): bool
    {
        // Create notification
        $now = $this->convertNowToUtcDateTime();
        $dataInsert = [];
        foreach ($userIds as $userId) {
            $dataInsert[] = [
                'type' => $type,
                'content' => $content,
                'is_read' => false,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->model->insert($dataInsert);

        // Push notification to user
        $data = [];
        $fcmHelper = new FcmHelper();
        $deviceTokens = $this->model
            ->whereIn($this->primaryKey, $userIds)
            ->whereNotNull('device_token')
            ->pluck('device_token')
            ->toArray();
        $data['device_tokens'] = $deviceTokens;
        // TODO: Add more data to push notification
        $data['data_push'] = [];
        $fcmHelper->pushFcm($data);

        return true;
    }

    /**
     * Convert now to UTC date time. 
     *
     * @return UTCDateTime
     */
    public function convertNowToUtcDateTime(): UTCDateTime
    {
        return new UTCDateTime(Carbon::now()->timestamp * 1000);
    }
}
