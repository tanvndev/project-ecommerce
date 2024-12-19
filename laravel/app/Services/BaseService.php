<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class BaseService
 */
class BaseService implements BaseServiceInterface
{
    public function __construct() {}

    protected function createSEO(array $payload, string $title = 'name', string $description = 'description'): array
    {
        $payload['meta_title'] = $payload['meta_title'] ?? $payload[$title] ?? '';
        $payload['meta_description'] = $payload['meta_description'] ?? $payload[$description] ?? '';
        $payload['meta_title'] = truncate($payload['meta_title']);
        $payload['meta_description'] = truncate($payload['meta_description'], 160);

        return $payload;
    }

    protected function convertToCode(string $str): string
    {
        $newStr = Str::slug($str);
        $newStr = strtoupper(str_replace('-', '', $newStr));
        $newStr .= rand(0, 1000);

        return $newStr;
    }

    public function updateStatus()
    {
        return $this->executeInTransaction(function () {
            $request = request();

            $repositoryName = lcfirst($request->modelName) . 'Repository';
            $payload[$request->field] = $request->value;

            $this->{$repositoryName}->update($request->modelId, $payload);

            return successResponse(__('messages.publish.success'));
        }, __('messages.publish.error'));
    }

    public function updateStatusMultiple()
    {
        return $this->executeInTransaction(function () {
            $request = request();

            $repositoryName = lcfirst($request->modelName) . 'Repository';
            $payload[$request->field] = $request->value;

            $this->{$repositoryName}->updateByWhereIn('id', $request->modelIds, $payload);

            return successResponse(__('messages.publish.success'));
        }, __('messages.publish.error'));
    }

    public function deleteMultiple()
    {
        return $this->executeInTransaction(function () {
            $request = request();

            $repositoryName = lcfirst($request->modelName) . 'Repository';
            $forceDelete = ($request->has('forceDelete') && $request->forceDelete == '1')
                ? 'forceDeleteByWhereIn'
                : 'deleteByWhereIn';

            $this->{$repositoryName}->{$forceDelete}('id', $request->modelIds);

            return successResponse(__('messages.action.success'));
        }, __('messages.action.error'));
    }

    protected function executeInTransaction($callback, string $messageError = '')
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();

            return $result;
        } catch (Exception $e) {
            // getError($e);

            Log::error('>>Transaction failed<<', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                // 'trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();

            return errorResponse($messageError);
        }
    }

    public function handleArchiveMultiple()
    {
        return $this->executeInTransaction(function () {
            $request = request();

            $repositoryName = lcfirst($request->modelName) . 'Repository';

            $this->{$repositoryName}->restoreByWhereIn('id', $request->modelIds);

            return successResponse(__('messages.action.success'));
        }, __('messages.action.error'));
    }
}
