import http from 'k6/http';
import { sleep, check } from 'k6';
import { htmlReport } from 'https://raw.githubusercontent.com/benc-uk/k6-reporter/main/dist/bundle.js';
import { textSummary } from 'https://jslib.k6.io/k6-summary/0.0.2/index.js';

const TEST_TYPE = __ENV.TEST_TYPE || 'load';

export const options = {
  scenarios: {
    load_test: {
      executor: 'ramping-vus',
      startVUs: 0,

      stages:
        TEST_TYPE === 'stress'
          ? [
              { duration: '30s', target: 100 },
              { duration: '30s', target: 200 },
              { duration: '30s', target: 300 },
              { duration: '30s', target: 400 },
              { duration: '30s', target: 500 },
              { duration: '1m', target: 500 },
              { duration: '30s', target: 0 },
            ]
          : TEST_TYPE === 'spike'
          ? [
              { duration: '30s', target: 10 },
              { duration: '5s', target: 500 },
              { duration: '1m', target: 500 },
              { duration: '5s', target: 10 },
              { duration: '30s', target: 10 },
              { duration: '30s', target: 0 },
            ]
          : [
              { duration: '30s', target: 10 },
              { duration: '1m', target: 50 },
              { duration: '30s', target: 100 },
              { duration: '30s', target: 0 },
            ],
    },
  },

  thresholds: {
    http_req_duration: ['p(95)<500'],
    http_req_failed: ['rate<0.01'],
    checks: ['rate>0.99'],
  },
};

export default function () {
  let res = http.get('https://quickpizza.grafana.com');

  check(res, {
    'status is 200': (res) => res.status === 200,
  });

  sleep(1);
}

export function teardown(data) {
  // teardown code
}

export function handleSummary(data) {
  let reportName = 'result.html';

  if (TEST_TYPE === 'load') {
    reportName = 'load-result.html';
  } else if (TEST_TYPE === 'stress') {
    reportName = 'stress-result.html';
  } else if (TEST_TYPE === 'spike') {
    reportName = 'spike-result.html';
  }

  return {
    'result.html': htmlReport(data),
    [reportName]: htmlReport(data),
    stdout: textSummary(data, {
      indent: ' ',
      enableColors: true,
    }),
  };
}