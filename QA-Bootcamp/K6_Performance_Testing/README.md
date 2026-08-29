Tugas 11 - Praktik Performance Testing Menggunakan K6
Informasi Pengujian

Performance testing dilakukan menggunakan k6 terhadap QuickPizza:

https://quickpizza.grafana.com

Pengujian dilakukan dengan tiga jenis workload:

Load Testing
Stress Testing
Spike Testing

Functional validation dilakukan menggunakan check() untuk memastikan HTTP Status = 200.

Performance Requirement
Requirement	Target
p95 response time	< 500 ms
Error rate	< 1%
Check success rate	> 99%
Hasil Pengujian
1. Load Testing

Load testing dilakukan dengan peningkatan VU secara bertahap sampai 100 VU, kemudian diturunkan kembali ke 0 VU.

Metric	Hasil
Max VU	100
Total requests	4.084
Average response time	363.29 ms
p90	407.47 ms
p95	598.68 ms
Maximum	2.18 s
Error rate	0.00%
Check success	100.00%

HTTP status tetap 200 dan tidak terdapat request yang gagal. Namun, p95 sebesar 598.68 ms lebih tinggi dari requirement 500 ms, sehingga requirement response time belum terpenuhi.

2. Stress Testing

Stress testing dilakukan dengan meningkatkan jumlah pengguna secara bertahap sampai 500 VU.

Metric	Hasil
Max VU	500
Total requests	45.651
Average response time	561.10 ms
p90	773.70 ms
p95	984.94 ms
Maximum	31.78 s
Error rate	0.08%
Check success	99.91%

Pada stress testing, response time meningkat dibandingkan load testing. p95 menjadi 984.94 ms. Terdapat 39 request gagal dari 45.651 request, tetapi error rate masih 0.08%, sehingga masih di bawah batas 1%. Check success 99.91% juga masih memenuhi requirement.

3. Spike Testing

Spike testing dilakukan dengan memberikan lonjakan jumlah pengguna secara tiba-tiba sampai 500 VU, kemudian load diturunkan kembali.

Metric	Hasil
Max VU	500
Total requests	16.675
Average response time	822.17 ms
p90	1.76 s
p95	2.62 s
Maximum	4.92 s
Error rate	0.00%
Check success	100.00%

Pada spike testing, response time menjadi yang paling tinggi. p95 mencapai 2.62 detik, jauh di atas requirement 500 ms. Walaupun response time meningkat, seluruh request pada hasil ini tetap menghasilkan HTTP status 200 dan tidak terdapat request gagal.

Analisis
1. Bagaimana perubahan response time ketika load meningkat?

Response time meningkat ketika jumlah VU dan intensitas workload semakin besar.

Testing	Max VU	p95
Load	100	598.68 ms
Stress	500	984.94 ms
Spike	500	2.62 s

Pada load testing, p95 mencapai 598.68 ms. Saat beban dinaikkan sampai 500 VU pada stress testing, p95 meningkat menjadi 984.94 ms. Pada spike testing, lonjakan pengguna secara cepat menyebabkan p95 meningkat paling tinggi menjadi 2.62 detik.

2. Pada kondisi testing, di mana response time paling tinggi?

Response time paling tinggi terjadi pada Spike Testing. Nilai p95 mencapai 2.62 detik, sedangkan nilai maksimum mencapai 4.92 detik.

3. Apakah error mulai muncul ketika load semakin besar?

Ya. Pada load testing error rate sebesar 0.00%, sedangkan pada stress testing meningkat menjadi 0.08% dengan 39 request gagal dari 45.651 request. Pada spike testing error rate kembali menjadi 0.00%.

Walaupun terdapat error pada stress testing, error rate masih berada di bawah requirement 1%.

4. Pada level VU berapa mulai terlihat performance degradation?

Performance degradation sudah terlihat pada load testing sampai 100 VU, karena p95 mencapai 598.68 ms, sementara requirement adalah kurang dari 500 ms.

Namun, dari summary k6 yang tersedia tidak dapat ditentukan secara pasti apakah degradation pertama kali terjadi tepat pada 10, 50, atau 100 VU karena metric yang ditampilkan merupakan hasil agregasi seluruh tahap pengujian.

Pada 500 VU, degradation semakin jelas dengan p95 984.94 ms pada stress testing dan 2.62 detik pada spike testing.

5. Bagaimana perbedaan perilaku sistem antara Load, Stress, dan Spike Testing?

Load Testing menggunakan peningkatan beban secara bertahap sampai 100 VU. Sistem masih memberikan HTTP status 200 pada seluruh request dan error rate 0.00%, tetapi p95 sudah melewati requirement.

Stress Testing meningkatkan beban sampai 500 VU. Response time semakin tinggi dan mulai terdapat request yang gagal, tetapi error rate masih rendah yaitu 0.08%.

Spike Testing memberikan lonjakan pengguna secara tiba-tiba sampai 500 VU. Kondisi ini menghasilkan response time paling tinggi dengan p95 sebesar 2.62 detik.

Secara umum, sistem masih dapat memproses request dari sisi functional validation, tetapi performanya menurun ketika beban semakin besar atau terjadi lonjakan pengguna.

6. Setelah load diturunkan, apakah sistem dapat kembali stabil?

Konfigurasi workload menurunkan jumlah VU kembali sampai 0 VU setelah beban tinggi. Namun, summary k6 yang digunakan merupakan hasil agregasi seluruh periode pengujian sehingga tidak tersedia angka response time khusus pada fase recovery.

Karena itu, berdasarkan hasil yang tersedia, recovery tidak dapat dinyatakan secara pasti secara kuantitatif. Untuk memastikan recovery, diperlukan metric atau pengujian khusus pada fase setelah load diturunkan.

7. Berdasarkan hasil ketiga tes, apakah API masih memenuhi performance requirement?

API belum sepenuhnya memenuhi performance requirement.

Requirement p95 adalah kurang dari 500 ms, sedangkan hasilnya:

Load Testing: 598.68 ms ❌
Stress Testing: 984.94 ms ❌
Spike Testing: 2.62 s ❌

Requirement lainnya masih terpenuhi:

Check success Load: 100% ✅
Check success Stress: 99.91% ✅
Check success Spike: 100% ✅
Error rate Load: 0.00% ✅
Error rate Stress: 0.08% ✅
Error rate Spike: 0.00% ✅
Kesimpulan

API QuickPizza masih dapat memberikan HTTP status 200 dan memiliki error rate yang rendah pada workload yang diuji. Namun, dari sisi response time, API belum memenuhi performance requirement karena p95 melebihi 500 ms pada seluruh jenis pengujian.

Semakin tinggi dan semakin tiba-tiba beban yang diberikan, response time semakin meningkat. Kondisi paling berat terlihat pada Spike Testing dengan p95 2.62 detik.

Dengan demikian, berdasarkan hasil pengujian, API dinyatakan belum memenuhi performance requirement secara keseluruhan, terutama pada aspek response time.