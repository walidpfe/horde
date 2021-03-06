<?php
/**
 * Prepare the test setup.
 */
require_once dirname(__FILE__) . '/TestBase.php';

/**
 * @author     Jan Schneider <jan@horde.org>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Vcs
 * @subpackage UnitTests
 */

class Horde_Vcs_GitTest extends Horde_Vcs_TestBase
{
    public function setUp()
    {
        if (!self::$conf) {
            $this->markTestSkipped();
        }
        $this->vcs = Horde_Vcs::factory(
            'Git',
            array_merge(self::$conf,
                        array('sourceroot' => dirname(__FILE__) . '/repos/git')));
    }

    public function testFactory()
    {
        $this->assertInstanceOf('Horde_Vcs_Git', $this->vcs);
        $this->assertTrue($this->vcs->hasFeature('branches'));
        $this->assertFalse($this->vcs->hasFeature('deleted'));
        $this->assertTrue($this->vcs->hasFeature('patchsets'));
        $this->assertTrue($this->vcs->hasFeature('snapshots'));
        $this->assertFalse($this->vcs->hasFeature('foo'));
    }

    public function testDirectory()
    {
        $dir = $this->vcs->getDirObject('');
        $this->assertInstanceOf('Horde_Vcs_Directory_Git', $dir);
        $this->assertEquals(array('dir1'), $dir->queryDirList());
        $files = $dir->queryFileList();
        $this->assertInternalType('array', $files);
        $this->assertCount(1, $files);
        $this->assertInstanceOf('Horde_Vcs_File_Git', $files[0]);
        $this->assertCount(1, $dir->queryFileList(true));
        $this->assertEquals(array('branch1', 'master'), $dir->getBranches());

        /* Test non-existant directory. */
        /*
        try {
            $this->vcs->getDirObject('foo');
            $this->fail('Expected Horde_Vcs_Exception');
        } catch (Horde_Vcs_Exception $e) {
        }
        */
    }

    public function testFile()
    {
        $file = $this->vcs->getFileObject('foo');
        $this->assertInstanceOf('Horde_Vcs_File_Git', $file);
        //$this->assertEquals('file1', $files[0]->queryRepositoryName());
    }

    public function testLog()
    {
        $log = $this->vcs->getLogObject($this->vcs->getFileObject('foo'), '');
        $this->assertInstanceOf('Horde_Vcs_Log_Git', $log);
    }

    public function testPatchset()
    {
        try {
            $ps = $this->vcs->getPatchsetObject(array('file' => 'foo'));
            $this->fail('Expected Horde_Vcs_Exception');
        } catch (Horde_Vcs_Exception $e) {
        }
        //$this->assertInstanceOf('Horde_Vcs_Patchset_Git', $ps);
    }
}
