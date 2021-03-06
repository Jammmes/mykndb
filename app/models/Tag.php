<?php

class Tag extends Model
{
    public function getTags($user_id)
    {
        $sql = "SELECT COUNT(lt.tag_id) AS quant, t.id, t.title
        FROM `link_tags` lt
        RIGHT JOIN `tags` t  ON lt.tag_id = t.id 
        RIGHT JOIN `users` u ON u.id = t.user_id 
        WHERE u.id = ?
        GROUP BY t.id ORDER BY quant DESC";
       
       return['error'=>'','content'=> DB::getInstance()->Select($sql,[$user_id])];
    }
    
    /** Проверка нового тега
     * 
     * @param string $tagName - Имя тега
     * @param int $user_id - ИД пользователя
     * @return boolean
     */
    protected function checkNewTag($tagName,$user_id)
    {
        $result = false;
        
        $sql = "SELECT `id` FROM `tags` WHERE `title` = ? AND `user_id` = ?";
        
        $data = DB::getInstance()->Select($sql,[$tagName,$user_id]);
        
        $id = $data ? $data[0]['id']: 0;
        
        if($id == 0){
            $result = true;
        }
        
        return $result;
    }
    
    /** Добавление нового тега
     * 
     * @param string $tagName - Имя тега
     * @param int $user_id - ИД пользователя
     * @return array
     */
    public function addTag($tagName,$user_id)
    {
        $result = [];
        $isCorrect = $this->checkNewTag($tagName, $user_id);
        
        if ($isCorrect){
            $query = "INSERT INTO `tags` (`id`,`title`,`user_id`)
            VALUES (NULL, ?,?)";
            $result = 
                    ['error'=>'', 
                    'content' => DB::getInstance()->Query($query,[$tagName,$user_id])
                    ];
        }else{
            $result = ['error'=>"The tag '".$tagName."' is already exists"];
        }
        
        return $result;
    }
    
    public function deleteTag($id)
    {
       $query = "DELETE FROM `tags` WHERE `id` = ?";
       return ['error' =>'','content'=>DB::getInstance()->Query($query,[$id])];
       
    }
    
    public function editTag($id, $user_id,$tagName)
    {
        $result = [];
        $isCorrect = $this->checkNewTag($tagName, $user_id);      

        if ($isCorrect){
            $query = "UPDATE `tags` SET `title` = ? WHERE `id` = ?";
            $result = 
                    ['error'=>'', 
                    'content' => DB::getInstance()->Query($query,[$tagName,$id])
                    ];
        }else{
            $result = ['error'=>"The tag '".$tagName."' is already exists"];
        }
        
        return $result;    
    }
      
    public function findTags($data,$user_id)
    {
        $arrTags = explode(',', $data);
        
        foreach ($arrTags as &$item){
          $item = trim($item);
        }
        
        $tag = $arrTags[count($arrTags)-1];
        
        $sql = "SELECT `title` FROM `tags` WHERE
        `title` like CONCAT('%',?,'%') AND `user_id` = ?";
        $result = DB::getInstance()->Select($sql,[$tag,$user_id]);
        
        return $result;
    }
    
}

