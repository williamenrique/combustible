<?php
/**
 * Modelo para la gestión de Bienes (Activos).
 * Se encarga de todas las interacciones con la base de datos relacionadas con el inventario de bienes.
 */
class BienesModel extends Mysql {
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Selecciona todos los bienes activos del inventario con sus datos relacionados.
     * @return array - Lista de todos los bienes.
     */
    public function selectBienes(): array {
        $sql = "SELECT 
                    b.id_bien,
                    b.descripcion_bien,
                    d.departamento_bien,
                    g.grupo,
                    sg.subgrupo,
                    s.seccion,
                    b.fecha_adquisicion,
                    b.status_bien
                FROM table_bienes_inventario b
                INNER JOIN table_bienes_departamentos d ON b.bien_depatamento_id = d.depatamento_bien_id
                INNER JOIN table_bienes_grupo g ON b.grupo_id = g.id_grupo
                INNER JOIN table_bienes_subgrupo sg ON b.subgrupo_id = sg.subgrupo_id
                INNER JOIN table_bienes_seccion s ON b.seccion_id = s.seccion_id
                WHERE b.status = 1";
        $request = $this->select_all($sql);
        return $request;
    }

    /**
     * Selecciona un bien específico por su ID.
     * @param int $id_bien - El ID del bien a seleccionar.
     * @return array|null - Los datos del bien o null si no se encuentra.
     */
    public function selectBien(int $id_bien) {
        $sql = "SELECT * FROM table_bienes_inventario WHERE id_bien = ? AND status = 1";
        return $this->select($sql, [$id_bien]);
    }

    /**
     * Inserta un nuevo bien en la base de datos.
     * @param array $data - Datos del bien a insertar.
     * @return int - El ID del bien insertado.
     */
    public function insertBien(array $data): int {
        $query_insert = "INSERT INTO table_bienes_inventario(bien_depatamento_id, grupo_id, subgrupo_id, seccion_id, descripcion_bien, fecha_adquisicion, status_bien, user_id, status) VALUES(?,?,?,?,?,?,?,?,1)";
        $arrData = array(
            $data['bien_depatamento_id'],
            $data['grupo_id'],
            $data['subgrupo_id'],
            $data['seccion_id'],
            $data['descripcion_bien'],
            $data['fecha_adquisicion'],
            $data['status_bien'],
            $data['user_id']
        );
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    /**
     * Actualiza un bien existente en la base de datos.
     * @param array $data - Datos del bien a actualizar, incluyendo su ID.
     * @return bool - True si la actualización fue exitosa.
     */
    public function updateBien(array $data): bool {
        $sql = "UPDATE table_bienes_inventario 
                SET bien_depatamento_id = ?, 
                    grupo_id = ?, 
                    subgrupo_id = ?, 
                    seccion_id = ?, 
                    descripcion_bien = ?, 
                    fecha_adquisicion = ?, 
                    status_bien = ? 
                WHERE id_bien = ?";
        $arrData = array(
            $data['bien_depatamento_id'],
            $data['grupo_id'],
            $data['subgrupo_id'],
            $data['seccion_id'],
            $data['descripcion_bien'],
            $data['fecha_adquisicion'],
            $data['status_bien'],
            $data['id_bien']
        );
        $request = $this->update($sql, $arrData);
        return $request;
    }

    /**
     * Realiza una eliminación lógica de un bien (cambia status a 0).
     * @param int $id_bien - El ID del bien a eliminar.
     * @return bool - True si la eliminación fue exitosa.
     */
    public function deleteBien(int $id_bien): bool {
        $sql = "UPDATE table_bienes_inventario SET status = 0 WHERE id_bien = ?";
        return $this->update($sql, [$id_bien]);
    }

    /**
     * Obtiene la lista de departamentos de bienes activos.
     * @return array
     */
    public function getDepartamentos(): array {
        $sql = "SELECT depatamento_bien_id, departamento_bien FROM table_bienes_departamentos WHERE departamento_status = 1";
        return $this->select_all($sql);
    }

    /**
     * Obtiene la lista de grupos de bienes activos.
     * @return array
     */
    public function getGrupos(): array {
        $sql = "SELECT id_grupo, grupo FROM table_bienes_grupo WHERE grupo_status = 1";
        return $this->select_all($sql);
    }

    /**
     * Obtiene la lista de subgrupos de bienes activos.
     * @return array
     */
    public function getSubgrupos(): array {
        $sql = "SELECT subgrupo_id, subgrupo FROM table_bienes_subgrupo WHERE subgrupo_status = 1";
        return $this->select_all($sql);
    }

    /**
     * Obtiene la lista de secciones de bienes activos.
     * @return array
     */
    public function getSecciones(): array {
        $sql = "SELECT seccion_id, seccion FROM table_bienes_seccion WHERE seccion_status = 1";
        return $this->select_all($sql);
    }
}