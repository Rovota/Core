<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support\Traits;

trait Errors
{

   protected array $errors = [];

   protected array $error_messages = [];

   // -----------------

   public function addErrorMessage(string $type, string $identifier, string $message): static
   {
      $this->error_messages[$type][$identifier] = trim($message);
	   return $this;
   }

   public function addErrorMessages(array $items): static
   {
      foreach ($items as $type => $item) {
         foreach ($item as $identifier => $message) {
            $this->addErrorMessage($type, $identifier, $message);
         }
      }
	   return $this;
   }

   public function getErrorMessage(string $type, string $identifier): string|null
   {
      return $this->error_messages[$type][$identifier] ?? null;
   }

   public function getErrorMessages(string $type, array|null $identifiers = null): array
   {
      if ($identifiers === null) {
         return $this->error_messages[$type];
      }

      $messages = [];
      foreach ($identifiers as $identifier) {
         if (isset($this->error_messages[$type][$identifier])) {
            $messages[] = $this->getErrorMessage($type, $identifier);
         }
      }
      return $messages;
   }

   public function hasErrorMessage(string $type, string $identifier): bool
   {
      return isset($this->error_messages[$type][$identifier]);
   }

   public function hasErrorMessages(string $type, array|null $identifiers = null): bool
   {
      if ($identifiers === null) {
         return empty($this->error_messages[$type]) === false;
      }

      foreach ($identifiers as $identifier) {
         if ($this->hasErrorMessage($type, $identifier) !== true) {
            return false;
         }
      }
      return true;
   }

   public function clearErrorMessage(string $type, string $identifier): static
   {
      unset($this->error_messages[$type][$identifier]);
	  return $this;
   }

   public function clearErrorMessages(string|null $type = null): static
   {
      if (is_string($type)) {
         unset($this->error_messages[$type]);
      } else {
         $this->error_messages = [];
      }
	   return $this;
   }

   // -----------------

   public function addError(string $type, string $message, array $data = []): static
   {
      $identifier = $message;
      $message = $this->getErrorMessage($type, $message) ?? $message;
      $this->errors[$type][$identifier] = ['message' => $message , 'data' => $data];
	  return $this;
   }

   public function addErrors(array $errors): static
   {
      foreach ($errors as $type => $fields) {
         if (!isset($fields['data'])) {
            $fields['data'] = [];
         }
         $this->addError($type, $fields['message'], $fields['data']);
      }
	  return $this;
   }

   public function getErrors(string|null $type = null): array
   {
      if ($type === null) {
         return $this->errors;
      }
      return $this->errors[$type] ?? [];
   }

   public function getFormattedErrors(string|null $type = null, string|null $source = null): array
   {
      $formatted = [];
      if ($type === null) {
         foreach ($this->errors as $type => $errors) {
            foreach ($errors as $identifier => $error) {
               $formatted[$type][$identifier] = __($error['message'], $error['data'], $source);
            }
         }
      } else {
         if (isset($this->errors[$type])) {
            foreach ($this->errors[$type] as $identifier => $error) {
               $formatted[$identifier] = __($error['message'], $error['data'], $source);
            }
         }
      }
      return $formatted;
   }

   public function hasErrors(string|null $type = null): bool
   {
      if ($type === null) {
         return empty($this->errors) === false;
      }
      return (isset($this->errors[$type])) && empty($this->errors[$type]) === false;
   }

   public function clearError(string $type, string $identifier): static
   {
      unset($this->errors[$type][$identifier]);
	  return $this;
   }

   public function clearErrors(string|null $type = null): static
   {
      if (is_string($type)) {
         unset($this->errors[$type]);
      } else {
         $this->errors = [];
      }
	  return $this;
   }

   // -----------------

   public function forceSetErrors(array $errors): static
   {
      $this->errors = $errors;
	  return $this;
   }

   public function passErrors(array $errors): static
   {
      foreach ($errors as $name => $item) {
         foreach ($item as $identifier => $data) {
            $this->errors[$name][$identifier] = $data;
         }
      }
	  return $this;
   }

}