<?php

declare(strict_types=1);

namespace Rovota\Core\Http;

/**
 * @internal
 * Creates an organized version of the messy and confusing $_FILES array.
 * @author Travis Van Couvering (github.com/tvanc)
 * @author Rovota (modification for usage in Rovota Core)
 */
final class FilesArrayOrganizer
{

   public static function organize(array $filesArray): array
   {
      $output = [];
      $flattenedFilesArray = [];

      foreach ($filesArray as $top_level_name => $attributes) {
         $output[$top_level_name] = [];

         foreach ($attributes as $attribute_name => $attribute_values) {
            FilesArrayOrganizer::inner(
               $output[$top_level_name],
               $attribute_name,
               $attribute_values,
               $flattenedFilesArray
            );
         }
      }

      foreach ($flattenedFilesArray as & $file) {
         $file = new UploadedFile($file['name'], $file['type'], $file['tmp_name'], $file['error']);
      }

      return $output;
   }


   private static function inner(array &$root_element, string $attribute_name, mixed $value, array &$files = [], array $path = []): void
   {
      // If $value is not an array then we've arrived at the attribute value
      if (!is_array($value)) {
         $last_key = $attribute_name;
         $stage = &$root_element;

         // With each element of $path, go one stage deeper into $root_element
         foreach ($path as $path_segment) {
            // If the stage doesn't exist yet, create it
            if (!isset($stage[$path_segment])) {
               $stage[$path_segment] = [];
            }
            $stage = &$stage[$path_segment];
         }

         // Add the final stage to the $files array
         if (!in_array($stage, $files)) {
            $files[] = &$stage;
         }
         $stage[$last_key] = $value;

         return;
      }

      // If $value is an array, recurse into it, building up $infix_path
      foreach ($value as $child_field_index => $child_field_values) {
         // Create new array one path segment longer, without mutating $path
         $infix_path = array_merge($path, [$child_field_index]);

         FilesArrayOrganizer::inner(
            $root_element,
            $attribute_name,
            $child_field_values,
            $files,
            $infix_path
         );
      }
   }

}