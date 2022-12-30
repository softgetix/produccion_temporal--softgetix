<?php
$sBox1 = array(0xf1,0x6e,0xaf,0xb1,0x6d,0x4c,0xd5,0x4f,0xe1,0xf9,0xc9,0xbc,0x21,0x6b,0x2d,0x68,
0x00,0xe2,0xad,0x61,0x7d,0x5e,0x10,0x28,0x51,0xba,0xbf,0xea,0x16,0x43,0x83,0x0e,
0x91,0x6c,0x9f,0x31,0xe5,0x11,0x24,0xf6,0xa1,0xcf,0x6f,0x96,0x1c,0xab,0x55,0xc8,
0x17,0xf3,0x41,0x30,0x9d,0x2a,0x32,0xd1,0x20,0x03,0x5b,0x8d,0xb9,0xa0,0x5d,0x80,
0x39,0x7f,0xdd,0xd9,0x08,0x70,0x22,0x85,0x2c,0xac,0x40,0x18,0x47,0xbb,0x4a,0xe4,
0x4e,0x1a,0x36,0xef,0xa5,0xaa,0xf5,0x5c,0x09,0x5f,0xcb,0x1f,0xbd,0xda,0xf7,0xdf,
0x50,0x34,0x05,0xeb,0x57,0x1b,0x81,0x58,0xc1,0xed,0xb5,0x4d,0x60,0x35,0x06,0x9b,
0x37,0x73,0x01,0x53,0x67,0xa4,0x1d,0xde,0x66,0x95,0x8f,0xbe,0x42,0x33,0x02,0x74,
0x48,0xd7,0x71,0xee,0xc5,0xa6,0x3c,0xb3,0x44,0x23,0x59,0x12,0x7c,0x7b,0x88,0x19,
0x38,0x2b,0x04,0x13,0x6a,0xb0,0xc7,0xd0,0x99,0x89,0x8e,0x77,0x56,0xe9,0xdb,0x87,
0x0c,0x8a,0x93,0xa9,0xa2,0xf8,0xff,0x97,0x86,0xc2,0xe3,0x79,0x52,0xc4,0x64,0x75,
0x45,0xa7,0x5a,0xfa,0x7e,0xcd,0xd3,0x82,0x49,0xec,0x98,0x3e,0x3a,0xdc,0xc3,0x94,
0x3f,0x84,0x9a,0x15,0x9c,0x0d,0x76,0x1e,0xc6,0xd6,0xe7,0xfd,0xb2,0xb4,0xcc,0x0b,
0x29,0x26,0xb7,0xf4,0x8c,0xa8,0x78,0xe8,0xc0,0xd8,0xce,0x8b,0xa3,0xb8,0x2f,0xb6,
0x92,0xd2,0xca,0x4b,0xd4,0x63,0x0f,0x25,0x2e,0x27,0x69,0x0a,0xe6,0x54,0x72,0xae,
0xe0,0x07,0x9e,0x90,0xf0,0xf2,0x3b,0xfe,0x14,0x46,0xfb,0x62,0x7a,0x65,0xfc,0x3d);

$sBox2 = array(0x71,0xdb,0xa5,0x28,0x39,0x14,0x95,0x64,0xe9,0xe6,0xf7,0xc6,0xd9,0x2e,0xf5,0xd2,
0x31,0x3c,0xbf,0x20,0x61,0x08,0x00,0xdd,0xf1,0x91,0xd7,0xc7,0x8d,0x7b,0x81,0x33,
0xb1,0xaa,0xff,0x0c,0xe5,0x56,0x9d,0x0a,0x15,0x86,0x2d,0x78,0x45,0x16,0xe3,0x4b,
0xe1,0xe8,0x7d,0x4e,0x0e,0xf9,0x5f,0x29,0x13,0xb8,0x69,0x53,0x49,0x02,0xc9,0x84,
0x18,0x4c,0x57,0xee,0xbd,0x0d,0x1a,0x63,0xa9,0x62,0xb5,0xd0,0x19,0xa8,0xb7,0x41,
0xa1,0x87,0xcd,0x17,0x24,0x1e,0x4f,0x66,0x05,0x26,0x22,0x23,0x32,0x0b,0x3d,0x3b,
0x2a,0xd3,0x73,0xa6,0x59,0x04,0x93,0x47,0x99,0x89,0x68,0xc8,0x6d,0xcc,0xed,0x72,
0x60,0x2b,0xad,0xac,0x35,0xb2,0x83,0x30,0x25,0xba,0xbb,0xb0,0xfd,0xf2,0x51,0xa4,
0x40,0xf0,0x7c,0xd6,0x4a,0x6b,0x3a,0xea,0x03,0x97,0xab,0xef,0x3f,0x9f,0x3e,0x92,
0xd1,0xde,0x8a,0x09,0x6e,0xeb,0x9b,0xb9,0x6a,0xe2,0x90,0x88,0x50,0x5d,0xc1,0x54,
0x80,0x10,0xdf,0xa7,0x94,0xc2,0xf3,0xe0,0x37,0x38,0x8c,0x58,0x8e,0x06,0xb3,0xfc,
0x9a,0x7f,0xfb,0x9e,0x44,0x82,0x67,0xb6,0x11,0xaf,0xa0,0x42,0x1d,0xcb,0x27,0xcf,
0x79,0x77,0x96,0xf4,0x34,0xce,0xbc,0xec,0xae,0x36,0x5e,0x7e,0x65,0xfa,0x46,0x1c,
0x07,0xc3,0x85,0xc4,0x21,0x2c,0x01,0x52,0xc0,0x6f,0x6c,0xbe,0x48,0x98,0x0f,0xf8,
0xd8,0xe7,0xd5,0xdc,0x8f,0xf6,0xda,0x5c,0xca,0x55,0xd4,0x76,0xa3,0x4d,0x8b,0x12,
0x1b,0x70,0x5a,0x2f,0xb4,0xc5,0x74,0x7a,0x43,0x1f,0x75,0x5b,0xa2,0xfe,0x9c,0xe4);

$sBox3 = array(0x63,0x67,0x71,0xa2,0x69,0x55,0x54,0x51,0x2c,0x01,0x1c,0xbd,0x50,0x9d,0xa3,0x9f,0x41,0x9c,0x4d,0x46,
0xa3,0x0c,0x65,0xe8,0xa5,0x73,0x24,0x05,0x82,0x05,0xd7,0xbd,0xdb,0x73,0x12,0x78,0xd8,0x00,0xf3,0x4a,
0x1e,0x76,0x22,0x1f,0x90,0x90,0x54,0xa5,0xfa,0x47,0x92,0x8c,0x8a,0xd5,0xfc,0x30,0x48,0xda,0x17,0x43,
0xec,0xaf,0x7c,0x03,0xee,0x4b,0x32,0x27,0x3f,0x66,0xc2,0x6d,0x46,0xfd,0x5b,0x47,0x77,0xfc,0xbd,0x81,
0x91,0x42,0x7c,0xc8,0x86,0x6a,0x98,0xb5,0x0e,0x67,0xb4,0xd6,0x4a,0x08,0x67,0x92,0xd5,0x24,0x73,0x12,
0x15,0x32,0xe6,0x72,0x21,0xed,0xe2,0x7c,0xdc,0x32,0x92,0xca,0x5b,0x66,0x9c,0x93,0x4a,0x52,0xff,0x11,
0x8a,0x58,0xb5,0x36,0xe7,0x70,0x8e,0x26,0xfe,0xf8,0x14,0x9e,0x46,0xad,0x6d,0xd4,0xda,0xdb,0x19,0x64,
0x67,0xa6,0x68,0xde,0xe5,0xef,0x1a,0x15,0x7a,0xba,0x86,0x21,0xb1,0x97,0x27,0xdc,0x69,0xee,0x7f,0xba,
0x91,0x49,0x25,0x38,0x34,0x50,0x77,0x52,0x57,0x3f,0xb5,0x40,0x21,0x72,0x13,0xdc,0xee,0xc1,0xef,0xfa,
0xb2,0x77,0x57,0x85,0x41,0xee,0x69,0xc5,0xf9,0xae,0xd6,0x14,0xe5,0x26,0x0d,0xcd,0x32,0x3c,0xb6,0x6e);
$sPassword = "HGKcfwggJqTMsFTG";

$clave1='00000';
$clave2='00000';