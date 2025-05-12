<script setup lang="ts">
import type { Project } from '@/types';

defineProps<{ projects: Project[] }>();

const options = {
  series: [
    {
      data: [
        {
          x: 'Analysis',
          y: [new Date('2019-02-27').getTime(), new Date('2019-03-04').getTime()],
          fillColor: '#008FFB',
        },
        {
          x: 'Design',
          y: [new Date('2019-03-04').getTime(), new Date('2019-03-08').getTime()],
          fillColor: '#00E396',
        },
        {
          x: 'Coding',
          y: [new Date('2019-03-07').getTime(), new Date('2019-03-10').getTime()],
          fillColor: '#775DD0',
        },
        {
          x: 'Testing',
          y: [new Date('2019-03-08').getTime(), new Date('2019-03-12').getTime()],
          fillColor: '#FEB019',
        },
        {
          x: 'Deployment',
          y: [new Date('2019-03-12').getTime(), new Date('2019-03-17').getTime()],
          fillColor: '#FF4560',
        },
      ],
    },
  ],
  chart: {
    height: 350,
    type: 'rangeBar',
  },
  plotOptions: {
    bar: {
      horizontal: true,
      distributed: true,
      dataLabels: {
        hideOverflowingLabels: false,
      },
    },
  },
  dataLabels: {
    enabled: true,
    formatter: function (val, opts) {
      // console.log(val)
      const label = opts.w.globals.labels[opts.dataPointIndex];
      // const a = new Date(val[0])
      // const b = new Date(val[1])
      // const diff = b.diff(a, 'days');
      const diff = (val[1] - val[0]) / (1000 * 3600 * 24);
      return label + ': ' + diff + (diff > 1 ? ' days' : ' day');
    },
    style: {
      colors: ['#f3f4f5', '#fff'],
    },
  },
  xaxis: {
    type: 'datetime',
  },
  yaxis: {
    show: false,
  },
  grid: {
    row: {
      colors: ['#f3f4f5', '#fff'],
      opacity: 1,
    },
  },
};
</script>

<template>
  <div class="chart-container">
    <apexchart :options="options" :series="options.series"></apexchart>
  </div>
</template>
<style scoped>
.chart-container {
  width: 760px;
  max-width: 100%;
  margin: 0 auto 3rem;
}
</style>
