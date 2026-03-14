<?php

return [
  'instructor_intro_video_max_kb' => (int) env('INSTRUCTOR_INTRO_VIDEO_MAX_KB', 512000),

  // supported file sources
  'storage_source' => [
    'upload' => 'Upload',
    'wasabi' => 'Wasabi',
    'aws' => 'AWS S3',
    'youtube' => 'YouTube',
    'vimeo' => 'Vimeo',
    'external_link' => 'External Link',
    'google_drive' => 'Google Drive',
    'iframe' => 'IFrame',
  ],

  // supported file types
  'file_types' => [
    'video' => 'Video',
    'audio' => 'Audio',
    'pdf' => 'PDF',
    'txt' => 'Txt File',
    'docx' => 'Docx',
    'image' => 'Image',
    'iframe' => 'Iframe',
    'file' => 'File',
    'other' => 'Other',
  ],
  'live_types' => [
    'zoom' => 'Zoom',
    'jitsi' => 'Jitsi',
  ]
];
