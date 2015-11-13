<?php

exec('mongoimport --db mva_test --collection restaurants --drop --file ' . __DIR__ . '/dataset.json');
