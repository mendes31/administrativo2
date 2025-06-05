<?php

namespace App\adms\Helpers;

/**
 * Converter a o nome da imagem.
 *
 * Esta classe é responsável por converter um nome de imagem, que segue o formato de "slug".
 *
 * @package App\adms\Helpers
 * @author Rafael Mendes <raffaell_mendez@hotmail.com>
 */
class SlugImg
{
    /** @var string $text Recebe o texto que dever converter para o SLUG */
    private string $text;

    /** @var array $format Recebe o array de caracteres especiais que devem ser substituido */
    private array $format;

    /**
     * Metodo faz a conversão da informação em SLUG
     *
     * @param string $text
     * @return string|null
     */
    public function slug(string $text): string|null
    {
        $this->text = $text;

        $this->format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]?;:,\\\'<>°ºª';
        $this->format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr-----------------------------------------------------------------------------------------------';
        $this->text = strtr(utf8_decode($this->text), utf8_decode($this->format['a']), $this->format['b']);
        $this->text = str_replace(" ", "-", $this->text);
        $this->text = str_replace(array('-----', '----', '---', '--'), '-', $this->text);
        $this->text = strtolower($this->text);

        return $this->text;
    }
}
