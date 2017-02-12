<?php

namespace App\Classes;

use App\Document;
use App\Carousel;
use App\NewsAttachment;
use App\KnowledgeAttachment;
use stdClass;

class UploadDocument
{
    public static function documentLists($documentType) {
        $docs = Document::where('document_type_id', $documentType)
            ->orderBy('position', 'asc')
            ->get();

        $documents = [];

        foreach ($docs as $doc) {
            $document = new stdClass();
            $document->id = $doc->id;
            $document->display = $doc->display;
            $document->file = $doc->file;
            $document->position = $doc->position;

            array_push($documents, $document);
        }

        return $documents;
    }

    public static function lastOrder($type, $documentType = 0) {
        $max = 0;

        switch ($type) {
            default:
                $max = Document::where('document_type_id', $documentType)
                    ->count();
                break;
            case 1:
                $max = Carousel::count();
                break;
        }

        return $max + 1;
    }

    public static function insertDocument($display, $filename, $documentType, $order) {
        $document = Document::create([
            'document_type_id' => $documentType,
            'display' => $display,
            'file' => $filename,
            'position' => $order,
        ]);

        return $document->id;
    }

    public static function updateDocument($id, $display, $filename) {
        $file = Document::find($id);
        $oldFile = $file->file;
        $file->display = $display;
        $file->file = $filename;
        $file->save();

        return $oldFile;
    }

    public static function updateOther($id, $filename) {
        $file = Document::find($id);
        $oldFile = $file->file;
        $file->file = $filename;
        $file->save();
        
        return $oldFile;
    }

    public static function reorderDocument($id, $index) {
        $document = Document::find($id);
        $position = $index + 1;
        $start = ($document->position > $position) ? $position : $document->position;
        $end = ($document->position > $position) ? $document->position : $position;

        $friends = Document::where('document_type_id', $document->document_type_id)
            ->whereBetween('position', [$start, $end])
            ->where('id', '<>', $id)
            ->get();

        foreach ($friends as $friend) {
            $friend->update(['position' => ($document->position > $index) ? $friend->position + 1 : $friend->position - 1]);
        }

        $document->update(['position' => $position]);

        return $friends->count();
    }

    public static function base64_to_content($base64_string) {
        $data = explode(',', $base64_string);

        return base64_decode($data[1]);
    }

    public static function insertCarousel($document_id, $image, $order) {
        $carousel = Carousel::create([
            'document_id' => $document_id,
            'image' => $image,
            'position' => $order,
        ]);

        return $carousel->id;
    }

    public static function updateCarouselDocument($id, $document_id) {
        $carousel = Carousel::find($id);
        $carousel->document_id = $document_id;
        $carousel->save();
    }

    public static function updateCarouselImage($id, $imagename) {
        $carousel = Carousel::find($id);
        $oldImage = $carousel->image;
        $carousel->image = $imagename;
        $carousel->save();
        
        return $oldImage;
    }

    public static function reorderCarousel($id, $index) {
        $carousel = Carousel::find($id);
        $position = $index + 1;
        $start = ($carousel->position > $position) ? $position : $carousel->position;
        $end = ($carousel->position > $position) ? $carousel->position : $position;

        $friends = Carousel::whereBetween('position', [$start, $end])
            ->where('id', '<>', $id)
            ->get();

        foreach ($friends as $friend) {
            $friend->update(['position' => ($carousel->position > $index) ? $friend->position + 1 : $friend->position - 1]);
        }

        $carousel->update(['position' => $position]);

        return $friends->count();
    }

    public static function reindexDocument($id) {
        $document = Document::find($id);
        $friends = Document::where('document_type_id', $document->document_type_id)
            ->where('position', '>', $document->position)
            ->get();

        foreach ($friends as $friend) {
            $friend->position = $friend->position - 1;
            $friend->save();
        }
    }

    public static function reindexCarousel($id) {
        $carousel = Carousel::find($id);
        $friends = Carousel::where('position', '>', $carousel->position)
            ->get();

        foreach ($friends as $friend) {
            $friend->position = $friend->position - 1;
            $friend->save();
        }
    }

    public static function attachFile($parent_type, $parent_id, $attach_type, $filename, $display) {
        switch ($parent_type) {
            default:
                $attachment = NewsAttachment::create([
                    'news_id' => $parent_id,
                    'attach_type' => $attach_type,
                    'file' => $filename,
                    'display' => $display
                ]);
                break;
            case 'knowledge':
                $attachment = KnowledgeAttachment::create([
                    'knowledge_id' => $parent_id,
                    'attach_type' => $attach_type,
                    'file' => $filename,
                    'display' => $display
                ]);
                break;
        }

        return $attachment->id;
    }
}