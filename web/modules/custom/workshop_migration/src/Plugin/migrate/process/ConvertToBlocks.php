<?php

namespace Drupal\workshop_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Convert HTML to Gutenberg formatted HTML.
 *
 * @MigrateProcessPlugin(
 *   id = "convert_to_blocks"
 * )
 */
class ConvertToBlocks extends ProcessPluginBase
{
  /**
   * Helper function to generate Gutenberg's wrapping comments.
   * 
   * @param DomDocument $document
   * @param string $block_name
   * @param array $attributes
   * 
   * @return DomComment[]
   */
  protected function generateGutenbergComments($document, $block_name, $attributes = NULL)
  {
    // Note the trailing space in the comment text. Required by Gutenberg.
    $startCommentNode = $document->createComment(' wp:' . $block_name . ' ');
    if (isset($attributes)) {
      $startCommentNode = $document->createComment(' wp:' . $block_name . ' ' . json_encode($attributes) . ' ');
    }

    $endCommentNode = $document->createComment(' /wp:' . $block_name . ' ');

    return [$startCommentNode, $endCommentNode];
  }

  /**
   * Helper function to add Gutenberg comments to basic HTML elements.
   * Does also some formatting for better support.
   * Avoiding the use of regex to parse HTML.
   * See: https://blog.codinghorror.com/parsing-html-the-cthulhu-way/
   *
   * @param $content
   *
   * @return array|mixed|string|string[]
   *
   * @todo replace this with Gutenberg's parser when available.
   */
  protected function addGutenbergComments($content)
  {
    $doc = new \DOMDocument();
    $doc->loadHTML($content);
    $body = $doc->getElementsByTagName('body')->item(0);

    // Let's go through each "root" element and add the appropriate tags.
    foreach ($body->childNodes as $node) {
      $comments = NULL;

      switch ($node->nodeName) {
        case 'h1':
        case 'h2':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
          $heading_level = (int)filter_var($node->nodeName, FILTER_SANITIZE_NUMBER_INT);
          $comments = $this->generateGutenbergComments($doc, 'heading', ['level' => $heading_level]);
          break;
        case 'p':
          $comments = $this->generateGutenbergComments($doc, 'paragraph');
          break;
        case 'ul':
          $comments = $this->generateGutenbergComments($doc, 'list');
          break;
        case 'ol':
          $comments = $this->generateGutenbergComments($doc, 'list', ['ordered' => true]);
          break;
        case 'blockquote':
          $comments = $this->generateGutenbergComments($doc, 'quote');
          $node->setAttribute('class', 'wp-block-quote');
          break;
        case 'pre':
          $node->setAttribute('class', 'wp-block-code');
          $comments = $this->generateGutenbergComments($doc, 'code');
          break;
        case 'table':
          $comments = $this->generateGutenbergComments($doc, 'table');
          $figure = $doc->createElement('figure');
          $figure->setAttribute('class', 'wp-block-table');
          $node->parentNode->replaceChild($figure, $node);
          $figure->appendChild($node);
          $node = $figure;
          break;
      }

      if (isset($comments)) {
        $node->before($comments[0]);
        $node->after($comments[1]);
      }
    }

    $content = $doc->saveHTML($body);

    // Remove the body tags.
    $content = str_replace('<body>', '', $content);
    $content = str_replace('</body>', '', $content);
    
    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {
    return $this->addGutenbergComments($value);
  }
}
