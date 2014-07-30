<?php

/**
 * Description of Paths
 *
 * @author developer
 */
class Paths {

    /**
     * Controller paths.
     */
    private static $controller_paths = array(
        CONTROLLERS_PATH
    );
    
    /**
     * View paths.
     */
    private static $view_paths = array(
        VIEWS_PATH
    );
    
    /**
     * Class paths.
     */
    private static $class_paths = array(
        CLASS_PATH,
    );
    
    /**
     * Model paths.
     */
    private static $model_paths = array(
        MODELS_PATH
    );
    
    /**
     * Library paths.
     */
    private static $library_paths = array(
        LIBRARY_PATH
    );

    /**
     * Get controller paths.
     */
    public static function getControllerPaths() {
        return self::$controller_paths;
    }

    /**
     * Get view paths.
     */
    public static function getViewPaths() {
        return self::$view_paths;
    }

    /**
     * Get class paths.
     */
    public static function getClassPaths() {
        return self::$class_paths;
    }

    /**
     * Get model paths.
     */
    public static function getModelPaths() {
        return self::$model_paths;
    }

    /**
     * Get library paths.
     */
    public static function getLibraryPaths() {
        return self::$library_paths;
    }

    /**
     * Add a controller path. 
     */
    public static function addControllerPath($path) {
        self::$controller_paths[] = $path;
    }

    /**
     * Add a view path.
     */
    public static function addViewPath($path) {
        self::$view_paths[] = $path;
    }

    /**
     * Add a class path.
     */
    public static function addClassPath($path) {
        self::$class_paths[] = $path;
    }

    /**
     * Add a model path.
     */
    public static function addModelPath($path) {
        self::$model_paths[] = $path;
    }

    /**
     * Add a library path.
     */
    public static function addLibraryPath($path) {
        self::$library_paths[] = $path;
    }

    /**
     * Remove a controller path.
     */
    public static function removeControllerPath($path) {
        Paths::removeFromPath(Paths::$controller_paths, $path);
    }

    /**
     * Remove a view path.
     */
    public static function removeViewPath($path) {
        Paths::removeFromPath(Paths::$view_paths, $path);
    }

    /**
     * Remove a class path.
     */
    public static function removeClassPath($path) {
        Paths::removeFromPath(Paths::$class_paths, $path);
    }

    /**
     * Remove a model path.
     */
    public static function removeModelPath($path) {
        Paths::removeFromPath(Paths::$model_paths, $path);
    }

    /**
     * Remove a library path.
     */
    public static function removeLibraryPaths($path) {
        Paths::removeFromPath(Paths::$library_paths, $path);
    }

    /**
     * Remove a path from an array of paths.
     */
    private static function removeFromPath(&$paths, $path) {
        for ($i = 0; $i < count(self::$paths); $i++) {
            if ($path == self::$paths[$i]) {
                unset(self::$paths[$i]);
                self::$paths = array_values(self::$paths);
            }
        }
    }

}
