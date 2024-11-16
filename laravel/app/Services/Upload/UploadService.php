<?php

namespace App\Services\Upload;

use App\Classes\Upload;
use App\Services\BaseService;
use App\Services\Interfaces\Upload\UploadServiceInterface;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService extends BaseService implements UploadServiceInterface
{
    public function paginate()
    {
        $request = request();

        $page = $request->page ?? 1;
        $pageSize = $request->pageSize ?? 30;
        $images = $this->getAllImages();

        if (empty($images)) {
            return [];
        }
        $currentPage = $page;
        $currentItems = array_slice($images, ($currentPage - 1) * $pageSize, $pageSize);

        // Tạo đối tượng LengthAwarePaginator để phân trang
        $paginator = new LengthAwarePaginator(
            $currentItems,          // Các mục trên trang hiện tại
            count($images),         // Tổng số mục trong mảng ban đầu
            $pageSize,               // Số lượng mục trên mỗi trang
            $currentPage,           // Trang hiện tại
            ['path' => $request->url()]   // Các tham số yêu cầu bổ sung cho URL phân trang
        );

        return successResponse('', $paginator);
    }

    private function getAllImages()
    {
        $imagePaths = Storage::allFiles('public'); // Lấy danh sách tất cả các tệp trong thư mục 'public' của storage
        $images = [];
        foreach ($imagePaths as $path) {

            // Lấy thông tin chi tiết về từng tệp ảnh
            $filename = basename($path);
            $size = Storage::size($path);
            $lastModified = Storage::lastModified($path);

            // Tạo các đường dẫn cho các kích thước ảnh
            $thumbnail = '?w=150&h=150';
            $medium = '?w=300&h=300';
            $large = '?w=500&h=500';

            // Đường dẫn mới phù hợp với cấu trúc bạn mong muốn
            $storedPath = Storage::url($path);
            $newPath = Str::replaceFirst('storage/uploads/photos/', 'images/', $storedPath);

            // Xây dựng dữ liệu cho từng ảnh và thêm vào mảng images
            $imageInfo = [
                'id'           => 'ID_' . $lastModified + $size,
                'url'          => asset($storedPath),
                'link'         => asset($newPath),
                'name'         => $filename,
                'size'         => $size,
                'lastModified' => $lastModified,
                'sizes'        => [
                    'thumbnail' => asset($newPath . $thumbnail),
                    'medium'    => asset($newPath . $medium),
                    'large'     => asset($newPath . $large),
                    'original'  => asset($newPath),
                ],
            ];

            $images[] = $imageInfo;
        }
        // Sắp xếp mảng theo thời gian sửa đổi cuối cùng (lastModified), giảm dần
        if (! empty($images)) {
            usort($images, function ($a, $b) {
                return $b['lastModified'] - $a['lastModified'];
            });
        }

        return $images;
    }

    public function create()
    {
        try {
            $payload = request()->except('_token');
            $files = $payload['files'] ?? null;
            $folderName = 'others';

            if (isset($payload['folder_name']) && ! empty($payload['folder_name'])) {
                $folderName = $payload['folder_name'];
            }

            // Xu ly anh resize
            if (isset($files) && ! empty($files)) {
                foreach ($files as $key => $file) {
                    $message = Upload::uploadImage($file, $folderName);
                    if ($message['status'] == 'error') {
                        $messages[] = $message['message'];
                    }
                }
            }
            $data = $this->paginate();

            return [
                'status'   => 'success',
                'messages' => $messages ?? [],
                'data'     => $data['data'],
            ];
        } catch (Exception $e) {
            return errorResponse(__('messages.upload.create.error'));
        }
    }

    public function destroy($id)
    {
        try {
            $url = request('url');
            $filePath = parse_url($url, PHP_URL_PATH);
            $filePath = str_replace('storage', 'public', $filePath);
            $filePath = ltrim($filePath, '/');

            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
            $data = $this->paginate();

            return successResponse(__('messages.upload.delete.success'), $data['data']);
        } catch (Exception $e) {
            return errorResponse(__('messages.upload.delete.error'));
        }
    }
}
