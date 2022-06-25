package program_code;

public class Consultant extends Specialist {
    private boolean securityNBU;

    public Consultant(String name, String education, String certificates, Float pricePerDay, int yearExperience, int monthExperience, boolean securityNBU) {
        super(name, education, certificates, pricePerDay, yearExperience, monthExperience);
        this.securityNBU = securityNBU;
    }

    public boolean isSecurityNBU() {
        return securityNBU;
    }

    public void setSecurityNBU(boolean securityNBU) {
        this.securityNBU = securityNBU;
    }
}