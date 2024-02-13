<?php

    namespace victorwesterlund;

    /*
        PHP eXtended Enums.
        The missing quality-of-life features from PHP 8+ Enums.
        https://github.com/victorwesterlund/php-xenum
    */
    trait xEnum {
        // Resolve enum case from enum name or return null
        public static function tryFromName(?string $name): ?static {
            foreach (self::cases() as $case) {
                if (strtoupper($name) === $case->name) {
                    return $case;
                }
            }

            // No matching case for name
            return null;
        }

        // Throw a ValueError if Enum name is not found
        public static function fromName(?string $name): static {
            $case = self::tryFromName($name);
            return $case ? $case : throw new ValueError("'{$name}' is not a valid case for enum " . self::class);
        }

        // Return array of enum names
        public static function names(): array {
            return array_column(self::cases(), "name");
        }

        // Return array of enum values
        public static function values(): array {
            return array_column(self::cases(), "value");
        }

        // Return assoc array of enum names and values
        public static function entries(): array {
            return array_combine(self::names(), self::values());
        }
    }