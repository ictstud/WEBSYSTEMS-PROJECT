<tbody>
    <?php
        $controller = new Controller();
        $users = $controller->readall();

        foreach($users as $user): 
    ?>
    <tr>
        <td><?=htmlspecialchars($user['ID'])?></td>
        <td><?=htmlspecialchars($user['last_name'])?></td>
        <td><?=htmlspecialchars($user['first_name'])?></td>
        <td><?=htmlspecialchars($user['file_name'])?></td>
        <td><?=htmlspecialchars($user['date_issued'])?></td>
        <td>

        <!--update-->
            <form action="../BackEnd/Controller.php?" method="get" style="display:inline;">
                <input type="hidden" name="method_finder" value="edit">
                <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                <button type="submit">EDIT</button>
            </form>

        <!--delete-->
            <form action="../BackEnd/Controller.php?" method="get" style="display:inline;">
                <input type="hidden" name="method_finder" value="delete">
                <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                <button type="submit">DELETE</button>
            </form>
        </td>
    </tr>
    <?php
        endforeach;
    ?>
</tbody>