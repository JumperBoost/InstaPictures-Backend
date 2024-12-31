<?php
namespace App\Utils;

class PostUtil {
    private static function getImageMediaArray(array $postMedia): array {
        return ["type" => "image", "url" => $postMedia["image_versions2"]["candidates"][0]["url"]];
    }

    private static function getVideoMediaArray(array $postMedia): array {
        return ["type" => "video", "url" => $postMedia["video_versions"][0]["url"]];
    }

    public static function getInformationFromApiArray(array $post): array {
        $info = [
            "code" => $post['code'],
            "text" => $post['caption'] ? $post['caption']['text'] : null,
            "username" => $post['user']['username'],
            "fullname" => $post['user']['full_name'],
            "pic" => $post['user']['hd_profile_pic_url_info']["url"],
            "likeCount" => $post['like_count'],
            "commentCount" => $post['comment_count']
        ];
        if($post['product_type'] == 'feed') {
            $info["medias"][] = self::getImageMediaArray($post);
        } else if($post['product_type'] == 'carousel_container') {
            foreach($post['carousel_media'] as $media) {
                if($media['media_type'] == 1) { // Image media
                    $info["medias"][] = self::getImageMediaArray($media);
                } else if($media['media_type'] == 2) { // Video media
                    $info["medias"][] = self::getVideoMediaArray($media);
                }
            }
        } else if($post['product_type'] == 'clips') {
            $info["medias"][] = self::getVideoMediaArray($post);
        }
        return $info;
    }
}