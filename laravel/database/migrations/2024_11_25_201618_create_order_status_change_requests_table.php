<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_status_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete(); // khóa ngoại đến bảng order
            $table->unsignedBigInteger('requested_by'); // ID của nhân viên đã yêu cầu thay đổi trạng thái.
            $table->string('current_status', 50); //Trạng thái hiện tại của đơn hàng tại thời điểm yêu cầu (VD: pending).
            $table->string('requested_status', 50); // Trạng thái mà nhân viên muốn thay đổi (VD: shipped).
            $table->text('reason'); // Lý do nhân viên đưa ra để yêu cầu thay đổi trạng thái.
            $table->text('rejection_reason')->nullable(); // Lý do mà quản trị viên đưa ra nếu từ chối yêu cầu (cột này có thể để trống nếu chưa xử lý hoặc được duyệt).
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected'); // Trạng thái của yêu cầu thay đổi (pending, approved, rejected).
            $table->unsignedBigInteger('approved_by')->nullable(); // ID của quản trị viên đã phê duyệt hoặc từ chối yêu cầu (nếu có).
            $table->timestamp('approved_at')->nullable(); // Thời gian mà yêu cầu được phê duyệt hoặc từ chối.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_change_requests');
    }
};
