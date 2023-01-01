<div class="modal" id="contactInviteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-fw fa-user-plus"></i> Invite Contact</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      
      <form action="post.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
        
        <div class="modal-body bg-white">
          
          <div class="form-group">
            <label>Email</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
              </div>
              <input type="email" class="form-control" name="email" placeholder="Email Address">
            </div>
          </div>

          <div class="form-group">
            <label>Welcome Letter</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-fw fa-envelope-open-text"></i></span>
              </div>
              <select class="form-control select2" name="welcome_letter">
                <option value="">- Select One -</option>
                <option value="">Standard</option>
                <option value="">Big Wig</option>
              </select>
            </div>
          </div>
        
        </div>

        <div class="modal-footer bg-white">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="invite_contact" class="btn btn-primary"><strong><i class="fas fa-paper-plane"></i> Send Invite</strong></button>
        </div>
      
      </form>
    
    </div>
  </div>
</div>