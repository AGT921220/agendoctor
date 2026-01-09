<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Agenda\UseCases\ListAvailableSlotsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListAvailableSlotsRequest;
use Illuminate\Http\JsonResponse;

final class AgendaController extends Controller
{
    public function availableSlots(ListAvailableSlotsRequest $request, ListAvailableSlotsUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute(
            dateYmd: (string) $request->validated('date'),
            durationMinutes: $request->validated('duration') !== null ? (int) $request->validated('duration') : null,
        );

        return response()->json([
            'date' => $result->date,
            'timezone' => $result->timezone,
            'duration_minutes' => $result->durationMinutes,
            'slots' => $result->slots,
        ]);
    }
}

