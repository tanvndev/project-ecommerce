<template>
    <canvas ref="canvas" class="snow-canvas"></canvas>
  </template>

  <script setup>
  import { onMounted, ref, onBeforeUnmount } from "vue";

  const canvas = ref(null);
  let ctx = null;
  let snowflakes = [];
  const snowflakeCount = 200;

  const createSnowflake = () => {
    const size = Math.random() * 3 + 2;
    const speed = Math.random() * 1 + 0.5;
    const x = Math.random() * window.innerWidth;
    const y = Math.random() * window.innerHeight;
    return { x, y, size, speed };
  };

  const drawSnowflakes = () => {
    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);

    // Vẽ mỗi bông tuyết
    snowflakes.forEach(snowflake => {
      ctx.beginPath();

      const x = snowflake.x;
      const y = snowflake.y;
      const size = snowflake.size;

      // Vẽ một hình ngôi sao đơn giản
      const spikes = 6; // Số lượng các cạnh của ngôi sao
      const step = Math.PI / spikes; // Góc của mỗi cánh ngôi sao
      const outerRadius = size;
      const innerRadius = size / 2;

      for (let i = 0; i < spikes; i++) {
        const angle = i * 2 * Math.PI / spikes;
        const xOuter = x + outerRadius * Math.cos(angle);
        const yOuter = y + outerRadius * Math.sin(angle);
        ctx.lineTo(xOuter, yOuter);

        const angleInner = angle + step;
        const xInner = x + innerRadius * Math.cos(angleInner);
        const yInner = y + innerRadius * Math.sin(angleInner);
        ctx.lineTo(xInner, yInner);
      }

      ctx.fillStyle = "white";
      ctx.fill();
      ctx.closePath();

      // Cập nhật vị trí bông tuyết
      snowflake.y += snowflake.speed;
      if (snowflake.y > window.innerHeight) {
        snowflake.y = -snowflake.size; // Đưa bông tuyết lên trên cùng
        snowflake.x = Math.random() * window.innerWidth; // Random hóa vị trí x
      }
    });
};

  const initSnowflakes = () => {
    snowflakes = [];
    for (let i = 0; i < snowflakeCount; i++) {
      snowflakes.push(createSnowflake());
    }
  };

  const animate = () => {
    drawSnowflakes();
    requestAnimationFrame(animate);
  };

  const resizeCanvas = () => {
    canvas.value.width = window.innerWidth;
    canvas.value.height = window.innerHeight;
  };

  onMounted(() => {
    canvas.value.width = window.innerWidth;
    canvas.value.height = window.innerHeight;
    ctx = canvas.value.getContext("2d");

    initSnowflakes();
    animate();
    window.addEventListener("resize", resizeCanvas);
  });

  onBeforeUnmount(() => {
    window.removeEventListener("resize", resizeCanvas);
  });
  </script>

  <style scoped>
  .snow-canvas {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    pointer-events: none;
  }
  </style>
