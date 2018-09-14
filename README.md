# myp-uop-format

Mythic Package File Format (.UOP)
---------------------------------

[1] Format Header
BYTE -> 'M'
BYTE -> 'Y'
BYTE -> 'P'
BYTE -> 0
DWORD -> Version
DWORD -> Signature?
QWORD -> Address of the first [2] Block
DWORD -> Max number of files per block
DWORD -> Number of files in this package
BYTE[]-> 0

[2] Block Header
DWORD -> Number of files in this block
QWORD -> Address of the next block

[3] File Header
QWORD -> Address of [4] Data Header
DWORD -> Length of file header
DWORD -> Size of compressed file
DWORD -> Size of decompressed file
QWORD -> File hash
DWORD -> Adler32 of [4a] Data Header in little endian, unknown in Version 5
WORD -> Compression type (0 - no compression, 1 - zlib)

[4] Data Header (Version 4)
WORD -> Data type
WORD -> Offset to data
QWORD -> File time (number of 100-nanosecond intervals since January 1, 1601 UTC)
BYTE (size of compressed file) -> File 

[4] Data Header (Version 5)
BYTE[]-> Metadata used by UO patcher

Pseudocode:
[1] Format Header

while ( Address of the next block > 0 )
[2] File Header

while ( Max number of files per block )
[3] File Header
end

while ( Number of files in this block )
[4] Data Header
end 
end
