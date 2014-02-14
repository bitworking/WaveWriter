WaveWriter
==========

RIFF Wave file writer 

Example:

```php
include 'BinaryFileWriter.php';
include 'WaveWriter.php';

$writer = new BinaryFileWriter('test.wav');
$wave = new WaveWriter($writer);

// write 2 second sine wave with 440Hz

$sampleCount = 44100 * 2;
$freqHz = 440;

$wave->startWrite($sampleCount);

// counters
$p = 0;
$t0 = $freqHz/44100;

for ($i=0;$i<$sampleCount;$i++) {
    $wave->writeSample(sin(2*M_PI*$p));

    $p += $t0;
    if ($p > 1) {
        $p -= 1;
    }
}

$wave->close();
```
