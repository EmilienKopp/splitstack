<?php

namespace App\Events;

use App\Concerns\ResolvesTranslucidPayload;
use App\Models\Landlord\Tenant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranslucidUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, ResolvesTranslucidPayload, SerializesModels;

    /**
     * The model instance.
     *
     * @var Model
     */
    public $model;

    /** @var array<string, mixed> */
    public array $changes = [];

    /**
     * Create a new event instance.
     */
    public function __construct(
        Model|array $model,
        public ?string $channel = null,
        public ?string $table = null,
        public ?string $modelClass = null,
        public ?string $keyColumn = null,
    ) {
        $this->model = $model;
        $this->changes = is_array($model) ? $model : $model->getChanges();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $space = Tenant::current()->space ?? 'unknown';
        $channel = $this->channel ?? 'translucid.'.$space;

        return [
            new PrivateChannel($channel),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => $this->resolveTableName(),
            'model' => $this->resolveModelClass(),
            'id' => $this->resolveKey(),
            'op' => 'updated',
            'changes' => $this->changes,
        ];
    }

    public function broadcastAs(): string
    {
        return 'translucid.updated.'.$this->resolveTableName().'.'.$this->resolveKey();
    }
}
