<?php
class Polling
{
        /**
         * Обработчики событий
         * @var array
         */
        protected $_handlers = array();
 
        /**
         * Генерация события
         * @param tstring $event Имя события
         * @param array $data Параметры события
         */
        public function push($event, $data = null)
        {
                /**
                 *  В памяти первые восемь байт содержат длину данных, сами данные хранятся как сериализованный массив вида
                 * array('evant_name1' => event_time, ..., '_updated' => event_time)
                 * где event_time - это время возникновения последнего события
                 * _updated - специальное поле, где хранится время возникновения последнего события
                 */
                $shmid = shmop_open(ftok(__FILE__, 'e'), 'c', 0644, 1024);
                if ($shmid) {
                        $storageLength = (int) shmop_read($shmid, 0, 8);
                        $storage = array();
                        if ($storageLength) {
                                $storage = unserialize(strval(shmop_read($shmid, 8, (int) $storageLength)));
                        }
                        $storage[$event] = array('time' => time(), 'data' => $data);
                        $storage['_updated'] = time();
                        $storageLength =  strlen(serialize($storage));
                        shmop_write($shmid,  (int) $storageLength, 0);
                        shmop_write($shmid, serialize($storage), 8);
                        shmop_close($shmid);
                }
        }
 
        /**
         * "Слежение"за возникновением событий. Время работы зависит от опции msx_execution_time
         * @param int $lastQueryTime Время завершения последнего запроса на "прослушивание".
         * Используется для того ,чтобы при медленном соединении не терялись события, возникшие
         * между запросами. Если не указан, то события отслеживаются с момента начала запроса
         * @return array Массив с результатами выполнения обработчиков для возникших событий
         */
        public function listen($lastQueryTime = null)
        {
                $lastQueryTime = ($lastQueryTime) ?: time();
                $endTime = time() + (int) ini_get('max_execution_time') -1;
                $shmid = shmop_open(ftok(__FILE__, 'e'), 'a', 0, 0);
                $result = array();
                if ($shmid) {
                        while (time() < $endTime) {
                                sleep(1);
                                $storageLength = (int) shmop_read($shmid, 0, 8);
                                $storage= unserialize(strval(shmop_read($shmid, 8, (int) $storageLength)));
                                if ($storage['_updated'] > $lastQueryTime) {
                                        unset($storage['_updated']);
                                        asort($storage);
                                        /**
                                         * Обход и выполнение зарегистрированных обработчиков событий
                                         */
                                        foreach ($storage as $event => $data) {
                                                if (substr($event, 0, 1) != '_' && $data['time'] > $lastQueryTime) {
                                                        $ret =$this->_runHandler($event, $data['data']);
                                                        if ($ret) {
                                                                $result[$event] = $ret;
                                                        }
                                                }
                                        }
                                        // При возникновении события завершаем запрос и отправляем данные клиенту
                                        break;
                                }
                        }
                        shmop_close($shmid);
                }
                return $result;
        }
 
        /**
         * Регистрация события и назначение обработчика. Поддерживается по одному обработчику на событие
         * @param string $event Имя события
         * @param callback $callback Функция-обработчик
         */
        public function registerEvent($event, $callback)
        {
                $this->_handlers[$event] = $callback;
        }
 
        /**
         * Запуск обработчика возникшего события
         * @param string $keyИмя события
         * @param array $data Параметры возникшего события
         * @return mixed
         */
        protected function _runHandler($key, $data = array())
        {
                if (!empty($this->_handlers[$key])) {
                        return call_user_func_array($this->_handlers[$key], array($data));
                }
        }
}
?>