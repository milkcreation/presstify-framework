<?php

declare(strict_types=1);

namespace tiFy\Support;

use InvalidArgumentException;
use Throwable;

class Html
{
    /**
     * @var bool
     */
    protected $escDoubleEncode = true;

    /**
     * @var string
     */
    protected $escEncoding = 'UTF-8';

    /**
     * @var int
     */
    protected $escFlags = ENT_QUOTES;

    /**
     * @var bool
     */
    protected $tagJson = false;

    /**
     * Conversion des caractères spéciaux.
     *
     * @param string|bool|array|object $value
     *
     * @return mixed
     */
    public static function e($value)
    {
        if (is_bool($value) || is_numeric($value) || is_null($value)) {
            return $value;
        }

        if (is_string($value)) {
            return (new static())->escape($value);
        }

        if (is_array($value)) {
            return array_map('self::e', $value);
        }

        if (is_object($value)) {
            if ($arrayValue = get_object_vars($value)) {
                return (object)array_map('self::e', $arrayValue);
            }
            return $value;
        }

        throw new InvalidArgumentException('Support HTML unable to escape the passed value type');
    }

    /**
     * Linéarisation des attributs de balises HTML.
     *
     * @param array $attr
     *
     * @return string
     */
    public static function attr(array $attr): string
    {
        return (new static())->tagAttributes($attr);
    }

    /**
     * Encodage des caractères spéciaux.
     *
     * @param string $value
     *
     * @return string
     */
    public function escape(string $value): string
    {
        return htmlspecialchars($value ?? '', $this->escFlags, $this->escEncoding, $this->escDoubleEncode);
    }

    /**
     * Formatage des attributs de balise HTML.
     *
     * @param array $attrs
     * @param bool $linearized
     *
     * @return string|array
     */
    public function tagAttributes(array $attrs, bool $linearized = true)
    {
        $attr = [];
        foreach ($attrs as $k => $v) {
            $attr[] = $this->walkAttr($v, $k);
        }

        return $linearized ? implode(' ', $attr) : $attr;
    }

    /**
     * Définition du double encodage lors de la conversion des caractères spéciaux.
     *
     * @param bool $doubleEncode
     *
     * @return $this
     */
    public function setEscapeDoubleEncode(bool $doubleEncode = true): self
    {
        $this->escDoubleEncode = $doubleEncode;

        return $this;
    }

    /**
     * Définition du l'encodage des caractères lors de la conversion des caractères spéciaux.
     *
     * @param string $encoding
     *
     * @return $this
     */
    public function setEscapeEncoding(string $encoding = 'UTF-8'): self
    {
        $this->escEncoding = $encoding;

        return $this;
    }

    /**
     * Définition du masque bit de traitement de la conversion des caractères spéciaux.
     *
     * @param int $flags
     *
     * @return $this
     */
    public function setEscapeFlags(int $flags = ENT_QUOTES): self
    {
        $this->escFlags = $flags;

        return $this;
    }

    /**
     * Définition de l'encodage de traitement des tableaux|objets passés en attributs de balise HTML.
     *
     * @param bool $json
     *
     * @return $this
     */
    public function setTagToJson(bool $json = true): self
    {
        $this->tagJson = $json;

        return $this;
    }

    /**
     * Encodage au format JSON.
     *{@internal La valeur de retour est exploitable en JS avec JSON.parse({{ value }})}
     *
     * @param array $value
     *
     * @return string
     */
    protected function jsonEncode(array $value): string
    {
        try {
            return json_encode($value, JSON_THROW_ON_ERROR);
        } catch(Throwable $e) {
            return '{}';
        }
    }

    /**
     * Encodage au format URL.
     * {@internal La valeur de retour est exploitable en JS avec JSON.parse(decodeURIComponent({{ value }})}
     *
     * @param array $value
     *
     * @return string
     */
    protected function urlEncode(array $value): string
    {
        return rawurlencode($this->jsonEncode($value));
    }

    /**
     * Conversion d'un d'attribut en attribut HTML.
     *
     * @param string|numeric|array $value
     * @param int|string $key
     *
     * @return string
     */
    protected function walkAttr($value, $key): string
    {
        if (is_array($value)) {
            $value = $this->tagJson ? $this->escape($this->jsonEncode($value)) : $this->urlEncode($value);
        }

        return is_numeric($key) ? (string)$value : "{$key}=\"{$value}\"";
    }
}