<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\ProhibitedWord;

use App\Services\BaseService;
use App\Services\Interfaces\ProhibitedWord\ProhibitedWordServiceInterface;
use App\Repositories\Interfaces\ProhibitedWord\ProhibitedWordRepositoryInterface;

class ProhibitedWordService extends BaseService implements ProhibitedWordServiceInterface
{
    protected $prohibitedWordRepository;
    protected $filePath;

    public function __construct(
        ProhibitedWordRepositoryInterface $prohibitedWordRepository,
    ) {
        $this->prohibitedWordRepository = $prohibitedWordRepository;
        $this->filePath = storage_path('prohibited_words.txt');
    }

    public function paginate()
    {
        $request = request();

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $select = ['id', 'keyword'];
        $pageSize = $request->pageSize;

        $data = $this->prohibitedWordRepository->pagination($select, $condition, $pageSize);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {

            $payload = $this->preparePayload();
            $this->prohibitedWordRepository->create($payload);

            $this->writeProhibitedWords();

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    private function writeProhibitedWords()
    {
        $prohibitedWords = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $newWord = request('keyword');

        $prohibitedWords[] = $newWord;

        file_put_contents($this->filePath, implode(PHP_EOL, $prohibitedWords) . PHP_EOL);
    }

    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            $payload = $this->preparePayload();

            $oldWord = $this->prohibitedWordRepository->findById($id);
            $this->prohibitedWordRepository->update($id, $payload);

            $this->updateProhibitedWordsInFile($payload['keyword'], $oldWord->keyword);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    private function updateProhibitedWordsInFile($newWord, $oldWord)
    {
        $prohibitedWords = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $index = array_search($oldWord, $prohibitedWords);
        if ($index !== false) {
            $prohibitedWords[$index] = $newWord;
        }

        file_put_contents($this->filePath, implode(PHP_EOL, $prohibitedWords) . PHP_EOL);
    }


    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');
        $payload = $this->createSEO($payload);

        return $payload;
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            $prohibitedWord = $this->prohibitedWordRepository->findById($id);
            $wordToDelete = $prohibitedWord->keyword;

            $this->prohibitedWordRepository->delete($id);

            $this->removeProhibitedWordFromFile($wordToDelete);

            return successResponse(__('messages.delete.success'));
        }, __('messages.delete.error'));
    }


    private function removeProhibitedWordFromFile($wordToDelete)
    {

        $prohibitedWords = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $key = array_search($wordToDelete, $prohibitedWords);
        if ($key !== false) {
            unset($prohibitedWords[$key]);
        }

        file_put_contents($this->filePath, implode(PHP_EOL, $prohibitedWords) . PHP_EOL);
    }
}
