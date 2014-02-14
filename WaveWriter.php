<?php

/**
 * helper class to write wav files
 *
 * @category    PHP
 * @package     WaveWriter
 * @author      Jan Fischer, bitWorking <info@bitworking.de>
 * @copyright   2014 Jan Fischer
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class WaveWriter
{
    
    protected $_binaryFileWriter;
    protected $_formatTag = 0x01; // PCM
    protected $_channels = 1;
    protected $_sampleRate = 44100;
    protected $_bitsPerSample = 16;
    protected $_bitResolution;

    public function __construct(BinaryFileWriter $binaryFileWriter, $channels = 1, $sampleRate = 44100, $bitsPerSample = 16)
    {
        $this->_binaryFileWriter = $binaryFileWriter;        
        $this->_channels = $channels;
        $this->_sampleRate = $sampleRate;
        $this->_bitsPerSample = $bitsPerSample;
    }
    
    protected function _writeHeader($sampleCount)
    {
        $this->_binaryFileWriter->setEndian(1); // LITTLE_ENDIAN
        $this->_bitResolution = (pow(2, $this->_bitsPerSample)/2)-1;
        $dataByteLength = $sampleCount * ($this->_bitsPerSample/8);
        $fileSize = 32 + 8 + $dataByteLength;
        
        // RIFF WAVE Chunk
        $this->_binaryFileWriter->writeString("RIFF");
        $this->_binaryFileWriter->writeInt($fileSize);
        $this->_binaryFileWriter->writeString("WAVE");
        // Format Chunk
        $this->_binaryFileWriter->writeString("fmt ");
        $this->_binaryFileWriter->writeInt(16);
        $this->_binaryFileWriter->writeShort($this->_formatTag);
        $this->_binaryFileWriter->writeShort($this->_channels);
        $this->_binaryFileWriter->writeInt($this->_sampleRate);
        $this->_binaryFileWriter->writeInt($this->_sampleRate * $this->_channels * $this->_bitsPerSample / 8);
        $this->_binaryFileWriter->writeShort($this->_channels * $this->_bitsPerSample / 8);
        $this->_binaryFileWriter->writeShort($this->_bitsPerSample);
        // Sound Data Chunk
        $this->_binaryFileWriter->writeString("data");
        $this->_binaryFileWriter->writeInt($dataByteLength);
    }

    public function writeData(array $data)
    {
        $this->_writeHeader(count($data));
        foreach ($data as $sample) {
            $this->writeSample($sample);
        }
    }
    
    public function startWrite($sampleCount)
    {
        $this->_writeHeader($sampleCount);
    }
    
    public function writeSample($sample)
    {
        if ($this->_bitsPerSample == 8) {
            $this->_binaryFileWriter->writeByte((int)(($sample * $this->_bitResolution) + $this->_bitResolution));
        }
        else if ($this->_bitsPerSample == 16) {
            $this->_binaryFileWriter->writeShort((int)($sample * $this->_bitResolution));
        }
        else if ($this->_bitsPerSample == 24) {
            $this->_binaryFileWriter->writeByte($sample & 0xFF);
            $this->_binaryFileWriter->writeByte($sample >> 8 & 0xFF);
            $this->_binaryFileWriter->writeByte($sample >> 16 & 0xFF);
        }
        else if ($this->_bitsPerSample == 32) {
            $this->_binaryFileWriter->writeInt($sample);
        }
    }
    
    public function close()
    {
        $this->_binaryFileWriter->close();
    }    
    
    
}