# UNISA-FC-2.0
UNISA FC 2.0 (UNISA Yogyakarta Feeder CSV 2.0) merupakan importer-eksporter Feeder berbasis CSV melalui Web Service v2.x. UNISA FC 2.0 didesain dapat beradaptasi dengan perubahan Web Service v2.x dan versi berikutnya tanpa perlu melakukan patch. UNISA FC 2.0 memanfaatkan GetDictionary untuk membuat CSV secara dinamis.
Selain itu, pada UNISA FC 2.0 juga disematkan Injector untuk memasukkan data dari Sistem Informasi Perguruan Tinggi berbasis MySQL/MariaDB ke dalam Feeder PDDIKTI.

Apabila Anda hanya membutuhkan Importer-Eksporter FEEDER berbasis CSV, maka:
- berikan informasi pada bagian SETTING UMUM pada berkas config.inc.php

Apabila Anda membutuhkan Injector dan Importer-Eksporter FEEDER berbasis CSV, maka
- berikan informasi pada bagian SETTING UMUM dan SETTING KHUSUS INJEKTOR pada berkas config.inc.php
