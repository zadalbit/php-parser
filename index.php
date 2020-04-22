<?php

$mysqli = new mysqli("localhost", "admin", "password", "ttm");

/* перевірка з'єднання */
if ($mysqli->connect_errno) {
    printf("Не вдалось підключитись: %s\n", $mysqli->connect_error);
    exit();
}

$file = file_get_contents('../vendor/symfony/symfony/src/Symfony/Component/HttpFoundation/FileBag.php');

$text = '<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * FileBag is a container for uploaded files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 */
class FileBag extends ParameterBag
{
    private static $fileKeys = array(\'error\', \'name\', \'size\', \'tmp_name\', \'type\');

    /**
     * Constructor.
     *
     * @param array $parameters An array of HTTP files
     */
    public function __construct(array $parameters = array())
    {
        $this->replace($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $files = array())
    {
        $this->parameters = array();
        $this->add($files);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        if (!is_array($value) && !$value instanceof UploadedFile) {
            throw new \InvalidArgumentException(\'An uploaded file must be an array or an instance of UploadedFile.\');
        }

        parent::set($key, $this->convertFileInformation($value));
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $files = array())
    {
        foreach ($files as $key => $file) {
            $this->set($key, $file);
        }
    }

    /**
     * Converts uploaded files to UploadedFile instances.
     *
     * @param array|UploadedFile $file A (multi-dimensional) array of uploaded file information
     *
     * @return array A (multi-dimensional) array of UploadedFile instances
     */
    protected function convertFileInformation($file)
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        $file = $this->fixPhpFilesArray($file);
        if (is_array($file)) {
            $keys = array_keys($file);
            sort($keys);

            if ($keys == self::$fileKeys) {
                if (UPLOAD_ERR_NO_FILE == $file[\'error\']) {
                    $file = null;
                } else {
                    $file = new UploadedFile($file[\'tmp_name\'], $file[\'name\'], $file[\'type\'], $file[\'size\'], $file[\'error\']);
                }
            } else {
                $file = array_map(array($this, \'convertFileInformation\'), $file);
            }
        }

        return $file;
    }

    /**
     * Fixes a malformed PHP $_FILES array.
     *
     * PHP has a bug that the format of the $_FILES array differs, depending on
     * whether the uploaded file fields had normal field names or array-like
     * field names ("normal" vs. "parent[child]").
     *
     * This method fixes the array to look like the "normal" $_FILES array.
     *
     * It\'s safe to pass an already converted array, in which case this method
     * just returns the original array unmodified.
     *
     * @param array $data
     *
     * @return array
     */
    protected function fixPhpFilesArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $keys = array_keys($data);
        sort($keys);

        if (self::$fileKeys != $keys || !isset($data[\'name\']) || !is_array($data[\'name\'])) {
            return $data;
        }

        $files = $data;
        foreach (self::$fileKeys as $k) {
            unset($files[$k]);
        }

        foreach ($data[\'name\'] as $key => $name) {
            $files[$key] = $this->fixPhpFilesArray(array(
                \'error\' => $data[\'error\'][$key],
                \'name\' => $name,
                \'type\' => $data[\'type\'][$key],
                \'tmp_name\' => $data[\'tmp_name\'][$key],
                \'size\' => $data[\'size\'][$key],
            ));
        }

        return $files;
    }
}
';
//$text = htmlspecialchars($file);

class Variable
{
    public $name, $type, $value;
    public $syntax;

    public function __construct()
    {
    	//
    }
}

class Comment
{
    public $value;
    public static $syntax = array(
    	array(
    		'opener' => '//', 
    		'closer' => PHP_EOL
    	),
    	array(
    		'opener' => '/*', 
    		'closer' => '*/'
    	),
    	array(
    		'opener' => '#', 
    		'closer' => PHP_EOL
    	)
    );

    public function __construct() {
    	//
    }
}
/*
assign value to var 
start comment 
declare class
declare function*/

/*class String
{
    public $number, $value, $command, $html;
    public $has_command_ending;

    public function __construct($number, $value) {
        $this->value = $value;
        $this->number = $number;

        $this->parse();
    }

    public function parse() {
    	//
    }
}*/

$commands_syntax = array(
	array(
		'command' => array(
			'name' => 'comment',
			'opener' => '//', 
			'closer' => PHP_EOL
		),
	),
	array(
		'command' => array(
			'name' => 'comment',
			'opener' => '/*', 
			'closer' => '*/'
		),
	),
	array(
		'command' => array(
			'name' => 'comment',
			'opener' => '#', 
			'closer' => PHP_EOL
		),
	),
	array(
		'scope' => 'public', 
		'type' => 'function',
		'sub_type' => 'abstract',
		
	),
	array(
		'scope' => 'public', 
		'type' => 'function',
		'sub_type' => 'static',
		
	),
);

$scopes = array(
	'public',
	'protected',
	'private',
	'global'
);

$operations_aims_types = array(
	'abstract',
	'static',
);

$operations_aims = array(
	'variable',
	'function',
	'method',
	'trait',
	'class',
	'property',
	'constant',
	'comment',
	'namespace',
	'use',
	'if',
	'else',
	'do_while',
	'while',
	'for',
	'foreach',
);

class Command
{
    public $number, $value;
    public static $syntaxes = array(
		array(
			'operation_aim' => 'variable',
			'opener' => 'global',
		),
		array(
			'operation_aim' => 'return',
			'opener' => 'return',
		),
		array(
			'operation_aim' => 'type',
			'opener' => 'abstract',
		),
		array(
			'operation_aim' => 'scope',
			'opener' => 'public',
		),
		array(
			'operation_aim' => 'scope',
			'opener' => 'protected',
		),
		array(
			'operation_aim' => 'scope',
			'opener' => 'private',
		),
		array(
			'operation_aim' => 'variable',
			'opener' => '$',
			'closer' => ';'
		),
		array(
			'operation_aim' => 'function',
			'opener' => 'function',
			'closer' => '}',
			'has_arguments' => true,
			'arguments_dividers' => array(
				',',
			),
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'method',
			'opener' => 'function',
			'closer' => '}',
			'has_arguments' => true,
			'arguments_dividers' => array(
				',',
			),
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'trait',
			'opener' => 'trait',
			'closer' => '}',
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'class',
			'opener' => 'class',
			'closer' => '}',
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'constant',
			'opener' => '_uppercase',
			'closer' => ';'
		),
		array(
			'operation_aim' => 'output_text',
			'opener' => '\'',
			'closer' => '\''
		),
		array(
			'operation_aim' => 'output_text',
			'opener' => '"',
			'closer' => '"'
		),
		array(
			'operation_aim' => 'comment',
			'opener' => '/*',
			'closer' => '*/'
		),
		array(
			'operation_aim' => 'comment',
			'opener' => '#',
			'closer' => PHP_EOL
		),
		array(
			'operation_aim' => 'comment',
			'opener' => '#',
			'closer' => PHP_EOL
		),
		array(
			'operation_aim' => 'namespace',
			'opener' => 'namespace',
			'closer' => ';'
		),
		array(
			'operation_aim' => 'use',
			'opener' => 'use',
			'closer' => ';'
		),
		array(
			'operation_aim' => 'if',
			'opener' => 'if',
			'closer' => '}',
			'has_arguments' => true,
			'arguments_dividers' => array(
				'||',
				'&&',
			),
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'else',
			'opener' => 'else',
			'closer' => '}',
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'do_while',
			'opener' => 'do',
			'closer' => ';',
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'while',
			'opener' => 'while',
			'closer' => '}',
			'has_arguments' => true,
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'for',
			'opener' => 'for',
			'closer' => '}',
			'has_arguments' => true,
			'arguments_dividers' => array(
				';',
			),
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'foreach',
			'opener' => 'foreach',
			'closer' => '}',
			'has_arguments' => true,
			'arguments_dividers' => array(
				'as',
			),
			'sub_code_opener' => '{'
		),
		array(
			'operation_aim' => 'function',
			'opener' => '_normalcase',
			'closer' => ';',
			'has_arguments' => true,
			'arguments_dividers' => array(
				',',
			),
		),
	);

    public function __construct($number, $value) {
    	$this->value = trim($value);
        $this->number = $number;

        $this->process();
    }

    public function process() {
    	if (empty($this->value)) {
    		return null;
    	}

    }
}

class Code
{
	public $pointer = 0;
	public $show = 0;
	public $operations = array();
	public $comments = array();
	public $output_text = array();
	public $commands = array();
	public $code = '';
	public $operation = array();
	public $operations_with_sub_code = array();
	public $mysql = array();

	public function __construct($code, $show) {
    	$this->code = trim($code);

        $this->show = $show;
        $this->parseCommands();

    }

    public function lookingForOutputTextCloser($operation, $pointer) {
    	$not_closer_pos = strpos($this->code, '\\'.$operation['closer'], $pointer);
		$closer_pos = strpos($this->code, $operation['closer'], $pointer);

		if ($not_closer_pos == 0 || $not_closer_pos > 0) {
			if (($not_closer_pos + 1) == $closer_pos) {
				$pointer = $closer_pos + strlen($operation['closer']);
				$operation = $this->lookingForOutputTextCloser($operation, $pointer);
			} else {
				$operation['closer_pos'] = $closer_pos;
			}
		} else {
			$operation['closer_pos'] = $closer_pos;
		}

		return $operation;
    }

    public function parseCommands() {
		if (empty($this->code)) {
			return 0;
		}

		$comments = array();

		foreach (Command::$syntaxes as $key => $operation) {
			$pointer = 0;

			while ($pointer >= 0) {
				if ($operation['opener'] == '_normalcase') {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				} else if ($operation['opener'] == '_uppercase') {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				} else {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				}

				if ($operation['opener_pos'] === 0 || $operation['opener_pos'] > 0) {
					$pointer = $operation['opener_pos'] + strlen($operation['opener']);

					if ($operation['operation_aim'] == 'comment') {
						$operation['closer_pos'] = strpos($this->code, $operation['closer'], $pointer);

						$pointer = $operation['closer_pos'] + strlen($operation['closer']);
						$this->comments[$operation['opener_pos']] = $operation;
					}
				} else {
					$pointer = -1;
				}
			}
		}

		foreach (Command::$syntaxes as $key => $operation) {
			$pointer = 0;

			while ($pointer >= 0) {
				if ($operation['opener'] == '_normalcase') {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				} else if ($operation['opener'] == '_uppercase') {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				} else {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				}

				if ($operation['opener_pos'] === 0 || $operation['opener_pos'] > 0) {
					$pointer = $operation['opener_pos'] + strlen($operation['opener']);
					$opener_is_comment = false;

					foreach ($this->comments as $key => $comment) {
						if ($comment['opener_pos'] < $operation['opener_pos'] && $comment['closer_pos'] > $operation['opener_pos']) {
							$opener_is_comment = true;
							$pointer = $comment['closer_pos'] + strlen($comment['closer']);
						}
					}

					if (!$opener_is_comment) {
						if ($operation['operation_aim'] == 'output_text') {
							$operation = $this->lookingForOutputTextCloser($operation, $pointer);
							
							$this->output_text[] = $operation;

							$pointer = $operation['closer_pos'] + strlen($operation['closer']);
						}
					}
				} else {
					$pointer = -1;
				}
			}
		}

		foreach (Command::$syntaxes as $key => $operation) {
			$pointer = 0;

			while ($pointer >= 0) {
				if (isset($operation['sub_code_opener'])) {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				
					if ($operation['opener_pos'] === 0 || $operation['opener_pos'] > 0) {
						$pointer = $operation['opener_pos'] + strlen($operation['opener']);

						$opener_is_comment = false;

						foreach ($this->comments as $key => $comment) {
							if ($comment['opener_pos'] < $operation['opener_pos'] && $comment['closer_pos'] > $operation['opener_pos']) {
								$opener_is_comment = true;
								$pointer = $comment['closer_pos'] + strlen($comment['closer']);
							}
						}

						$opener_is_output_text = false;

						foreach ($this->output_text as $key => $output_text) {
							if ($output_text['opener_pos'] < $operation['opener_pos'] && $output_text['closer_pos'] > $operation['opener_pos']) {
								$opener_is_output_text = true;
								$pointer = $output_text['closer_pos'] + strlen($output_text['closer']);
							}
						}

						if (!$opener_is_comment && !$opener_is_output_text && $operation['operation_aim'] != 'output_text') {
							$operation['sub_code_pos'] = strpos($this->code, $operation['sub_code_opener'], $pointer) + strlen($operation['sub_code_opener']);
							$pointer = $operation['sub_code_pos'];
							$this->operations_with_sub_code[$operation['opener_pos']] = $operation;
						}
					} else {
						$pointer = -1;
					}
				} else {
					$pointer = -1;
				}
			}
		}

		foreach (Command::$syntaxes as $key => $operation) {
			$pointer = 0;

			while ($pointer >= 0) {
				if ($operation['opener'] == '_normalcase') {
					$var_for_replacement = $operation;
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				} else if ($operation['opener'] == '_uppercase') {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				} else {
					$operation['opener_pos'] = strpos($this->code, $operation['opener'], $pointer);
				}

				if ($operation['opener_pos'] === 0 || $operation['opener_pos'] > 0) {
					$pointer = $operation['opener_pos'] + strlen($operation['opener']);

					$opener_is_comment = false;

					foreach ($this->comments as $key => $comment) {
						if ($comment['opener_pos'] < $operation['opener_pos'] && $comment['closer_pos'] > $operation['opener_pos']) {
							$opener_is_comment = true;
							$pointer = $comment['closer_pos'] + strlen($comment['closer']);
						}
					}

					$opener_is_output_text = false;

					foreach ($this->output_text as $key => $output_text) {
						if ($output_text['opener_pos'] < $operation['opener_pos'] && $output_text['closer_pos'] > $operation['opener_pos']) {
							$opener_is_output_text = true;
							$pointer = $output_text['closer_pos'] + strlen($output_text['closer']);
						}
					}

					$opener_is_argument = false;

					foreach ($this->operations_with_sub_code as $pos => $operation_with_sub_code) {
						if ($operation_with_sub_code['opener_pos'] < $operation['opener_pos'] && $operation_with_sub_code['sub_code_pos'] > $operation['opener_pos']) {
							$opener_is_argument = true;
							$pointer = $operation_with_sub_code['sub_code_pos'];
						}
					}

					if (!$opener_is_comment && !$opener_is_output_text && !$opener_is_argument && $operation['operation_aim'] != 'output_text') {
						if (isset($operation['sub_code_opener'])) {
							$pointer = strpos($this->code, $operation['sub_code_opener'], $pointer) + strlen($operation['sub_code_opener']);
						} else {
							if (isset($operation['closer'])) {
								$operation['closer_pos'] = strpos($this->code, $operation['closer'], $pointer);

								$pointer = $operation['closer_pos'];
							}
						}

						$this->operations[$operation['opener_pos']] = $operation;
					}
				} else {
					$pointer = -1;
				}
			}
		}

		if (count($this->operations) == 0) {
			return 0;
		}

		$pointer = 0;
		$unset_positions = array();
		// заміна невірно визначених команд
		while ($pointer >= 0) {
			preg_match('/([a-z_]+)([\s]*)(\()(.*)(\))([\s]*)(;)/', substr($this->code, $pointer), $matches, PREG_OFFSET_CAPTURE);

			if (count($matches) > 0) {
				$start = $matches[0][1] + $pointer;
				$length = strlen($matches[0][0]);
				$end = $start + $length;

				$pointer = $end;

				foreach ($this->operations as $position => $operation) {
					if (isset($operation['closer']) && !isset($operation['sub_code_opener'])) {
						$operation['closer_pos'] = strpos($this->code, $operation['closer'], $position);

						if ($operation['closer_pos'] <= $end && $position > $start) {
							$var_for_replacement['opener_pos'] = $start;
							$unset_positions[] = $position;
							$this->operations[$start] = $var_for_replacement;
						}
					}
				}
				
			} else {
				$pointer = -1;
			}
		}

		foreach ($unset_positions as $position) {
			unset($this->operations[$position]);
		}

		ksort($this->operations);
		
		$prev_operations = array();

		foreach ($this->operations as $position => $operation) {
			$operation['opener_pos'] = $position;

			if ($this->pointer <= $operation['opener_pos']) {
				$this->pointer = $operation['opener_pos'];
				if (isset($operation['closer'])) {
					if (count($prev_operations) > 0) {
						$operation['opener_pos'] = $prev_operations[0]['opener_pos'];
						$prev_operations = array();
					}

					if (isset($operation['sub_code_opener'])) {
						$operation['closer_pos'] = strpos($this->code, $operation['closer'], $this->pointer);
						$operation['sub_code_opener_pos'] = strpos($this->code, $operation['sub_code_opener'], $this->pointer);

						$operation = $this->checkIfCloserIsComment($operation);
						$operation = $this->checkIfCloserIsOutputText($operation);

						$this->operation = $operation;

						$start = $operation['sub_code_opener_pos'] + strlen($operation['sub_code_opener']);
						$end = $operation['closer_pos'];

						$prev_sub_operations = array();
						$has_openers = false;
						$has_sub_code_openers = false;

						for ($i = $start; $i <= $end; $i++) {
							
							if (isset($this->operations[$i])) {
								$has_openers = true;
								$operation = $this->operations[$i];

								if (isset($operation['closer'])) {
									if (count($prev_sub_operations) > 0) {
										$operation['opener_pos'] = $prev_sub_operations[0]['opener_pos'];
									}

									$prev_sub_operations = array();

									if (isset($operation['sub_code_opener'])) {
										$has_sub_code_openers = true;
										$i = $this->lookForOpenersBetween($operation, $start, $end, $i);
									} else {
										$operation['closer_pos'] = strpos($this->code, $operation['closer'], $i);

										$operation = $this->checkIfCloserIsComment($operation);
										$operation = $this->checkIfCloserIsOutputText($operation);

										$this->commands[] = substr($this->code, $operation['opener_pos'], $operation['closer_pos'] - $operation['opener_pos'] + strlen($operation['closer']));
										$this->pointer = $operation['closer_pos'] + strlen($operation['closer']);

										$i = $this->pointer;
									}
								} else {
									$prev_sub_operations[] = $operation;
								}
							}
						}
						
						if ($has_openers) {
							if (!$has_sub_code_openers) {
								$operation['closer_pos'] = $this->operation['closer_pos'];
								$command['starts_from'] = $this->operation['opener_pos'];
								$command['ends_on'] = $this->operation['closer_pos'] + strlen($this->operation['closer']);
								$command['length'] = $command['ends_on'] - $command['starts_from'];
								$command['has_sub_code'] = false;
								$command['text'] = substr($this->code, $this->operation['opener_pos'], $this->operation['closer_pos'] - $this->operation['opener_pos'] + strlen($this->operation['closer']));
								$command['details'] = $this->operation;

								$this->commands[] = $command['text'];
								$this->pointer = $command['ends_on'];

								$this->mysql[] = $command;

								/*$this->commands[] = substr($this->code, $this->operation['opener_pos'], $this->operation['closer_pos'] - $this->operation['opener_pos'] + strlen($this->operation['closer']));

								$this->pointer = $this->operation['closer_pos'] + strlen($this->operation['closer']);*/
							} else {
								$this->pointer = $i;
							}
						} else {
							$operation = $this->checkIfCloserIsComment($operation);
							$operation = $this->checkIfCloserIsOutputText($operation);

							$this->commands[] = substr($this->code, $this->operation['opener_pos'], $operation['closer_pos'] - $this->operation['opener_pos'] + strlen($operation['closer']));
							//construct
							$this->pointer = $operation['closer_pos'] + strlen($operation['closer']);
						}
					} else {
						$operation['closer_pos'] = strpos($this->code, $operation['closer'], $this->pointer);

						$operation = $this->checkIfCloserIsComment($operation);
						$operation = $this->checkIfCloserIsOutputText($operation);

						$command['starts_from'] = $operation['opener_pos'];
						$command['ends_on'] = $operation['closer_pos'] + strlen($operation['closer']);
						$command['length'] = $command['ends_on'] - $command['starts_from'];
						$command['has_sub_code'] = isset($operation['sub_code_opener']) ? true : false;
						$command['text'] = substr($this->code, $command['starts_from'], $command['length']);
						$command['details'] = $operation;

						$this->commands[] = $command['text'];
						$this->pointer = $command['ends_on'];

						$this->mysql[] = $command;
					}
				} else {
					$prev_operations[] = $operation;
				}
			}
		}
	}

	public function checkIfCloserIsOutputText($operation) {
		$closer_is_output_text = false;

		foreach ($this->output_text as $key => $output_text) {
			if ($output_text['opener_pos'] < $operation['closer_pos'] && $output_text['closer_pos'] > $operation['closer_pos']) {
				$closer_is_output_text = true;
				$pointer = $output_text['closer_pos'] + strlen($output_text['closer']);
			}
		}

		if ($closer_is_output_text) {
			$operation['closer_pos'] = strpos($this->code, $operation['closer'], $pointer);

			return $this->checkIfCloserIsOutputText($operation);
		} else {
			return $operation;
		}
	}

	public function checkIfCloserIsComment($operation) {
		$closer_is_comment = false;

		foreach ($this->comments as $key => $comment) {
			if ($comment['opener_pos'] < $operation['closer_pos'] && $comment['closer_pos'] > $operation['closer_pos']) {
				$closer_is_comment = true;
				$pointer = $comment['closer_pos'] + strlen($comment['closer']);
			}
		}

		if ($closer_is_comment) {
			$operation['closer_pos'] = strpos($this->code, $operation['closer'], $pointer);

			return $this->checkIfCloserIsComment($operation);
		} else {
			return $operation;
		}
	}

	public function lookForOpenersBetween($top_operation, $start, $end, $pointer) {
		$prev_sub_operations = array();

		$top_operation['closer_pos'] = strpos($this->code, $top_operation['closer'], $end + strlen($top_operation['closer']));
		$top_operation['sub_code_opener_pos'] = strpos($this->code, $top_operation['sub_code_opener'], $pointer);

		$top_operation = $this->checkIfCloserIsComment($top_operation);

		$sub_start = $top_operation['sub_code_opener_pos'] + strlen($top_operation['sub_code_opener']);
		$sub_end = $top_operation['closer_pos'];

		$has_openers = false;
		$has_sub_code_openers = false;

		for ($i = $sub_start; $i <= $sub_end; $i++) {
			if (isset($this->operations[$i])) {
				$has_openers = true;
				$operation = $this->operations[$i];

				if (isset($operation['closer'])) {
					if (count($prev_sub_operations) > 0) {
						$operation['opener_pos'] = $prev_sub_operations[0]['opener_pos'];
					}

					$prev_sub_operations = array();

					if (isset($operation['sub_code_opener'])) {
						$has_sub_code_openers = true;
						$i = $this->lookForOpenersBetween($operation, $sub_start, $sub_end, $i);
					} else {
						$operation['closer_pos'] = strpos($this->code, $operation['closer'], $i);

						$operation = $this->checkIfCloserIsComment($operation);

						$this->commands[] = substr($this->code, $operation['opener_pos'], $operation['closer_pos'] - $operation['opener_pos'] + strlen($operation['closer']));
						$this->pointer = $operation['closer_pos'] + strlen($operation['closer']);

						$i = $this->pointer;
					}
				} else {
					$prev_sub_operations[] = $operation;
				}
			}
		}

		if (!$has_openers || ($has_openers && !$has_sub_code_openers)) {
			$command['starts_from'] = $this->operation['opener_pos'];
			$command['ends_on'] = $top_operation['closer_pos'] + strlen($top_operation['closer']);
			$command['length'] = $command['ends_on'] - $command['starts_from'];
			$command['has_sub_code'] = isset($this->operation['sub_code_opener']) ? true : false;
			$command['text'] = substr($this->code, $command['starts_from'], $command['length']);
			$command['details'] = $this->operation;

			$this->commands[] = $command['text'];
			$this->pointer = $command['ends_on'];

			$this->mysql[] = $command;

			return $this->pointer;
		} else {
			return $i;
		}
	}
}

$code = new Code($text, 0);

foreach ($code->mysql as $key => $operation) {
	if ($operation['has_sub_code']) {
		$start = strpos($operation['text'], $operation['details']['sub_code_opener']);
		$sub_length = $operation['length'] - strlen($operation['details']['closer']) - $start;
		$sub_text = substr($operation['text'], $start, $sub_length);
		$sub_code = new Code($sub_text, 1);
	}
}

echo "<pre>";
var_dump($sub_code->mysql);
echo "</pre>";

$html = array();
$rows = explode(PHP_EOL, $text);

foreach ($rows as $index => $row) {
	$number = $index + 1;

	$html[] = $row;
}


?>