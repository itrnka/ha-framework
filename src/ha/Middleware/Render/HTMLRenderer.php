<?php
declare(strict_types = 1);

namespace ha\Middleware\Render;

use ha\Middleware\Middleware;

/**
 * Interface HTMLRenderer.
 * This interface is used for rendering HTML templates.
 */
interface HTMLRenderer extends Middleware
{

    /**
     * Render template as HTML.
     *
     * @param string $template Path to template
     * @param array $data Template data
     *
     * @return string HTML code
     */
    public function render(string $template, array $data): string;

    /**
     * Get native twig.
     * @return mixed
     */
    public function getNativeDriver();

}