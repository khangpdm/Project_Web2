-- Add staff management permission
INSERT INTO permission (permissionid, permissionname) VALUES ('NHANVIEN', 'Quản lý nhân viên');

-- Grant all funcs for staff management permission to admin powergroup 'GRP001'
INSERT INTO powergroup_func_permission (powergroupid, funcid, permissionid)
SELECT '1', f.funcid, 'NHANVIEN'
FROM func f
WHERE NOT EXISTS (
    SELECT 1 FROM powergroup_func_permission pfp
    WHERE pfp.powergroupid = '1' AND pfp.funcid = f.funcid AND pfp.permissionid = 'NHANVIEN'
);
