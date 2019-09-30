<?php
require APPPATH . 'libraries/Status.php';

class Materi_model extends CI_Model {

    public function getMateri()
    {
      $listMateri = [];
      $materi = $this->getListMateri();

      if($materi->num_rows()>0)
      {
        $list = [];
        foreach ($materi->result() as $key) {
          $listSub = [];
          $listInfo = [];
          $listMateriSub = $this->getMateriSub($key->id);
          $listMateriInfo = $this->getMateriInfo($key->id);

          if($listMateriSub->num_rows()>0)
          {
            $listSubResult = $listMateriSub->result();
            foreach ($listSubResult as $keySub) {
              $countPilgan = $this->countQuest($keySub->id_material,$keySub->id,'1')->result();
              $countEssay = $this->countQuest($keySub->id_material,$keySub->id,'2')->result();
              $listSubNew = [
                'id' => $keySub->id,
                'id_material' => $keySub->id_material,
                'name' => $keySub->name,
                'content' => $keySub->content,
                'created_at' => $keySub->created_at,
                'updated_at' => $keySub->updated_at,
                'time' => $keySub->time,
                'total_question' => $keySub->total_question,
                'total_question_multiple_choices' => $countPilgan[0]->total,
                'total_question_essay' => $countEssay[0]->total
              ];
              array_push($listSub,$listSubNew);
            }
          } else {
            $listSub = [];
          }

          if($listMateriInfo->num_rows()>0)
          {
            $listInfo = $listMateriInfo->result();
          } else {
            $listInfo = [];
          }

          $list = [
              'id' => $key->id,
              'icon' => $key->icon,
              'name' => $key->name,
              'description' => $key->description,
              'created_at' => $key->created_at,
              'updated_at' => $key->updated_at,
              'info' => $listInfo,
              'sub' => $listSub
          ];

          array_push($listMateri,$list);
        }
        return $listMateri;
      } else {
        return Status::MATERI_KOSONG;
      }
    }

    public function getMateriVideo()
    {
      $materi = $this->getListMateriVideo();

      if($materi->num_rows()>0)
      {
        return $materi;
      } else {
        return Status::MATERI_KOSONG;
      }
    }

    public function getListMateri()
    {
      $getMateri = "SELECT * FROM materials";
      $queryGetMateri = $this->db->query($getMateri);
      return $queryGetMateri;
    }

    public function getListMateriVideo()
    {
      $getMateri = "SELECT * FROM material_videos";
      $queryGetMateri = $this->db->query($getMateri);
      return $queryGetMateri;
    }

    public function countQuest($id_material, $id_material_subs, $id_question_category)
    {
      $getMateri = "SELECT count(id) as total FROM questions where id_material = ? and id_material_subs=? and id_question_category=?";
      $queryGetMateri = $this->db->query($getMateri, array($id_material, $id_material_subs, $id_question_category));
      return $queryGetMateri;
    }

    public function getMateriSub($id_material)
    {
      $getMateriSub = "SELECT a.*, COUNT(b.id) AS total_question FROM material_subs a LEFT JOIN questions b ON b.id_material_subs=a.id WHERE a.id_material = ? GROUP BY a.id";
      $queryGetMateriSub = $this->db->query($getMateriSub, array($id_material));
      return $queryGetMateriSub;
    }

    public function getMateriInfo($id_material)
    {
      $getMateriInfo = "SELECT * FROM material_infos WHERE id_material = ?";
      $queryGetMateriInfo = $this->db->query($getMateriInfo, array($id_material));
      return $queryGetMateriInfo;
    }
}
?>
