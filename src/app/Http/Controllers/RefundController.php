<?php

namespace App\Http\Controllers;

use App\Models\SkillOrder;
use App\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    private RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function store(Request $request, SkillOrder $skill_order): JsonResponse
    {
        $request->validate([
            'refund_amount' => 'required|integer|min:1',
            'refund_reason' => 'required|string',
        ]);

        try {
            $this->refundService->refundOrder(
                $skill_order,
                $request->input('refund_amount'),
                $request->input('refund_reason')
            );

            return response()->json(['message' => 'Refund processed successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Refund failed', 'error' => $e->getMessage()], 500);
        }
    }
}
